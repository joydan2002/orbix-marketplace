<?php
/**
 * Cloudinary Media Service
 * Handles all media uploads and management for Orbix platform
 */

require_once 'cloudinary-config.php';

class CloudinaryService {
    private $cloud_name;
    private $api_key;
    private $api_secret;
    private $environment;
    
    public function __construct() {
        $this->cloud_name = CLOUDINARY_CLOUD_NAME;
        $this->api_key = CLOUDINARY_API_KEY;
        $this->api_secret = CLOUDINARY_API_SECRET;
        $this->environment = getCloudinaryEnvironment();
    }
    
    /**
     * Upload image to Cloudinary
     */
    public function uploadImage($file, $public_id = null, $folder = null) {
        try {
            // Validate the file
            $validation = $this->validateImageFile($file);
            if (!$validation['success']) {
                return [
                    'success' => false,
                    'error' => $validation['error']
                ];
            }
            
            // Prepare file for upload
            if (is_string($file)) {
                // File path
                $file_path = $file;
                $file_name = basename($file);
            } else {
                // Uploaded file
                $file_path = $file['tmp_name'];
                $file_name = $file['name'];
            }
            
            // Generate public_id if not provided
            if (!$public_id) {
                $public_id = $this->generatePublicId($file_name, $folder);
            } else if ($folder) {
                $public_id = $folder . '/' . $public_id;
            }
            
            $timestamp = time();
            
            // Use signed upload method
            $uploadFolder = !empty($folder) ? "orbix/$folder" : 'orbix/products';
            
            $paramsToSign = [
                'folder' => $uploadFolder,
                'timestamp' => $timestamp
            ];
            
            ksort($paramsToSign);
            $paramsString = '';
            foreach ($paramsToSign as $key => $value) {
                $paramsString .= $key . '=' . $value . '&';
            }
            $paramsString = rtrim($paramsString, '&') . $this->api_secret;
            $signature = sha1($paramsString);
            
            $uploadData = [
                'file' => new CURLFile($file_path, $this->getMimeType($file_path), $file_name),
                'folder' => $uploadFolder,
                'api_key' => $this->api_key,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];
            
            $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloud_name}/image/upload";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                return [
                    'success' => false,
                    'error' => 'cURL Error: ' . $curlError
                ];
            }
            
            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                return [
                    'success' => true,
                    'public_id' => $responseData['public_id'],
                    'url' => $responseData['secure_url'],
                    'version' => $responseData['version'] ?? null,
                    'format' => $responseData['format'] ?? null,
                    'width' => $responseData['width'] ?? null,
                    'height' => $responseData['height'] ?? null,
                    'bytes' => $responseData['bytes'] ?? null
                ];
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = isset($errorData['error']['message']) ? $errorData['error']['message'] : "HTTP Error: $httpCode";
                return [
                    'success' => false,
                    'error' => $errorMessage
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Upload exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Upload video to Cloudinary
     */
    public function uploadVideo($file, $folder = 'videos', $options = []) {
        try {
            // Validate video file
            $validation = $this->validateVideoFile($file);
            if (!$validation['success']) {
                return $validation;
            }
            
            // Prepare upload data
            $folder_path = $this->environment['folder_prefix'] . CLOUDINARY_FOLDERS[$folder];
            $public_id = $this->generatePublicId($folder);
            
            $upload_data = [
                'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
                'upload_preset' => CLOUDINARY_VIDEO_PRESET,
                'folder' => $folder_path,
                'public_id' => $public_id,
                'resource_type' => 'video',
                'quality' => 'auto',
                'unique_filename' => true,
                'overwrite' => false
            ];
            
            // Add custom options
            $upload_data = array_merge($upload_data, $options);
            
            // Upload to Cloudinary
            $response = $this->makeUploadRequest(CLOUDINARY_VIDEO_UPLOAD_URL, $upload_data);
            
            if ($response && isset($response['secure_url'])) {
                return [
                    'success' => true,
                    'public_id' => $response['public_id'],
                    'secure_url' => $response['secure_url'],
                    'width' => $response['width'],
                    'height' => $response['height'],
                    'format' => $response['format'],
                    'duration' => $response['duration'] ?? 0,
                    'bytes' => $response['bytes'],
                    'folder' => $folder
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Video upload failed: ' . ($response['error']['message'] ?? 'Unknown error')
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Video upload exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Upload file to Cloudinary
     */
    public function uploadFile($file_path, $options = []) {
        try {
            if (!file_exists($file_path)) {
                throw new Exception("File not found: " . $file_path);
            }
            
            $this->validateImageFile($file_path);
            
            // Generate timestamp and signature
            $timestamp = time();
            $params = array_merge([
                'timestamp' => $timestamp,
                'upload_preset' => $this->upload_preset
            ], $options);
            
            // Remove null values
            $params = array_filter($params, function($value) {
                return $value !== null && $value !== '';
            });
            
            $signature = $this->generateSignature($params);
            $params['signature'] = $signature;
            $params['api_key'] = $this->api_key;
            
            // Create file upload data with proper MIME type
            $file_info = pathinfo($file_path);
            $mime_type = $this->getMimeType($file_path);
            
            $params['file'] = new CURLFile($file_path, $mime_type, $file_info['basename']);
            
            $url = "https://api.cloudinary.com/v1_1/{$this->cloud_name}/image/upload";
            
            error_log("✅ CloudinaryService: Uploading file: " . $file_path);
            error_log("✅ Upload URL: " . $url);
            error_log("✅ Upload preset: " . $this->upload_preset);
            
            $response = $this->makeUploadRequest($url, $params);
            
            if (!isset($response['secure_url'])) {
                throw new Exception("Upload successful but no secure_url in response");
            }
            
            error_log("✅ CloudinaryService: Upload successful - " . $response['secure_url']);
            return $response;
            
        } catch (Exception $e) {
            error_log("❌ CloudinaryService upload error: " . $e->getMessage());
            throw new Exception("Upload exception: " . $e->getMessage());
        }
    }
    
    /**
     * Get MIME type for file
     */
    private function getMimeType($file_path) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        
        // Fallback to extension-based detection
        if (!$mime_type) {
            $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ];
            $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
        }
        
        return $mime_type;
    }
    
    /**
     * Delete media from Cloudinary
     */
    public function deleteMedia($public_id, $resource_type = 'image') {
        try {
            $timestamp = time();
            $signature = $this->generateSignature([
                'public_id' => $public_id,
                'timestamp' => $timestamp
            ]);
            
            $delete_data = [
                'public_id' => $public_id,
                'api_key' => $this->api_key,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];
            
            $delete_url = "https://api.cloudinary.com/v1_1/{$this->cloud_name}/{$resource_type}/destroy";
            $response = $this->makeUploadRequest($delete_url, $delete_data);
            
            return [
                'success' => ($response['result'] ?? '') === 'ok',
                'result' => $response['result'] ?? 'failed'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Delete exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get optimized URL for display
     */
    public function getOptimizedUrl($public_id, $transformation = '', $resource_type = 'image') {
        if (empty($public_id)) return '';
        
        return buildCloudinaryUrl($public_id, $transformation, $resource_type);
    }
    
    /**
     * Validate uploaded image file
     */
    private function validateImageFile($file) {
        try {
            // Handle different file input types
            if (is_string($file)) {
                // File path
                if (!file_exists($file)) {
                    return ['success' => false, 'error' => 'File does not exist'];
                }
                $file_path = $file;
                $file_size = filesize($file);
                $file_name = basename($file);
            } else if (is_array($file)) {
                // Uploaded file array
                if (!isset($file['tmp_name']) || !isset($file['name'])) {
                    return ['success' => false, 'error' => 'Invalid file upload format'];
                }
                
                if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
                    return ['success' => false, 'error' => 'File upload error: ' . $this->getUploadErrorMessage($file['error'])];
                }
                
                $file_path = $file['tmp_name'];
                $file_size = $file['size'] ?? filesize($file_path);
                $file_name = $file['name'];
            } else {
                return ['success' => false, 'error' => 'Invalid file format - expected file path or upload array'];
            }
            
            // Check if file exists and is readable
            if (!file_exists($file_path) || !is_readable($file_path)) {
                return ['success' => false, 'error' => 'File is not accessible'];
            }
            
            // Check file size (10MB limit)
            $max_size = 10 * 1024 * 1024;
            if ($file_size > $max_size) {
                return ['success' => false, 'error' => 'File too large (max 10MB)'];
            }
            
            // Check file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $mime_type = $this->getMimeType($file_path);
            
            if (!in_array($mime_type, $allowed_types)) {
                return ['success' => false, 'error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP'];
            }
            
            // Verify it's actually an image
            $image_info = getimagesize($file_path);
            if ($image_info === false) {
                return ['success' => false, 'error' => 'File is not a valid image'];
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Validation error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get upload error message
     */
    private function getUploadErrorMessage($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
    
    /**
     * Validate video file
     */
    private function validateVideoFile($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 'No valid file uploaded'];
        }
        
        if ($file['size'] > MAX_VIDEO_SIZE) {
            return ['success' => false, 'error' => 'File size too large. Maximum 100MB allowed.'];
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, ALLOWED_VIDEO_TYPES)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_VIDEO_TYPES)];
        }
        
        return ['success' => true];
    }
    
    /**
     * Validate any file type
     */
    private function validateFile($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 'No valid file uploaded'];
        }
        
        // Allow up to 100MB for files (same as video)
        if ($file['size'] > 100 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File size too large. Maximum 100MB allowed.'];
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_file_types = [
            'zip', 'rar', '7z', 'tar', 'gz',  // Archives
            'pdf', 'doc', 'docx', 'txt',      // Documents
            'psd', 'ai', 'sketch', 'fig',     // Design files
            'html', 'css', 'js', 'json',      // Web files
            'php', 'py', 'java', 'cpp', 'c'   // Code files
        ];
        
        if (!in_array($file_extension, $allowed_file_types)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_file_types)];
        }
        
        return ['success' => true];
    }
    
    /**
     * Generate unique public ID
     */
    private function generatePublicId($folder) {
        return $folder . '_' . uniqid() . '_' . time();
    }
    
    /**
     * Get upload preset based on folder
     */
    private function getUploadPreset($folder) {
        switch ($folder) {
            case 'avatars':
                return CLOUDINARY_AVATAR_PRESET;
            case 'videos':
                return CLOUDINARY_VIDEO_PRESET;
            default:
                return CLOUDINARY_PRODUCT_PRESET;
        }
    }
    
    /**
     * Make upload request to Cloudinary
     */
    private function makeUploadRequest($url, $data) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_USERAGENT => 'Orbix-Cloudinary-Client/1.0'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        if ($curl_error) {
            error_log("❌ CloudinaryService cURL error: " . $curl_error);
            throw new Exception("cURL Error: " . $curl_error);
        }
        
        if ($http_code !== 200) {
            error_log("❌ CloudinaryService HTTP Error: " . $http_code);
            error_log("❌ Response body: " . $response);
            
            // Try to parse error response
            $error_data = json_decode($response, true);
            if ($error_data && isset($error_data['error']['message'])) {
                throw new Exception("HTTP Error: " . $http_code . " - " . $error_data['error']['message']);
            } else {
                throw new Exception("HTTP Error: " . $http_code);
            }
        }
        
        $decoded = json_decode($response, true);
        if (!$decoded) {
            error_log("❌ CloudinaryService: Invalid JSON response");
            throw new Exception("Invalid JSON response from Cloudinary");
        }
        
        return $decoded;
    }
    
    /**
     * Generate signature for authenticated requests
     */
    private function generateSignature($params) {
        ksort($params);
        $params_string = '';
        
        foreach ($params as $key => $value) {
            $params_string .= $key . '=' . $value . '&';
        }
        
        $params_string = rtrim($params_string, '&') . $this->api_secret;
        
        return sha1($params_string);
    }
    
    /**
     * Batch upload multiple files
     */
    public function batchUpload($files, $folder = 'products') {
        $results = [];
        
        foreach ($files as $index => $file) {
            if (is_array($file) && isset($file['tmp_name'])) {
                $result = $this->uploadImage($file, $folder);
                $results[$index] = $result;
            }
        }
        
        return $results;
    }
    
    /**
     * Get media info from Cloudinary
     */
    public function getMediaInfo($public_id, $resource_type = 'image') {
        try {
            $timestamp = time();
            $signature = $this->generateSignature([
                'public_id' => $public_id,
                'timestamp' => $timestamp
            ]);
            
            $url = "https://api.cloudinary.com/v1_1/{$this->cloud_name}/{$resource_type}/upload/{$public_id}?" . 
                   "api_key={$this->api_key}&timestamp={$timestamp}&signature={$signature}";
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30
            ]);
            
            $response = curl_exec($curl);
            curl_close($curl);
            
            return json_decode($response, true);
            
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

// Initialize global instance
$cloudinary = new CloudinaryService();
?>