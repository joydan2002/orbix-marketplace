# 9.4. UI Mockup and Components of Website

## Tổng quan Thiết kế UI/UX
Orbix Marketplace được thiết kế với phong cách **Glassmorphism** hiện đại, sử dụng gradient backgrounds, glass effects và typography đẹp mắt. Toàn bộ website sử dụng Tailwind CSS framework và Inter font family cho trải nghiệm người dùng nhất quán.

---

## 📱 **Nhóm 1: Trang Chính & Điều Hướng**

### 1.1 Homepage (index.php)
**Mô tả UI Mockup:**
- **Hero Section**: Gradient background với floating animations, header chính với logo "Orbix Market"
- **Navigation Bar**: Sticky header với menu đa cấp (Templates, Services, Support), search bar, user avatar/login
- **Featured Section**: Grid layout showcase các template/service nổi bật với thumbnail previews
- **Statistics Counter**: Animated counters hiển thị số lượng templates, sellers, downloads
- **Category Showcase**: Horizontal scrolling cards cho các danh mục sản phẩm
- **Testimonials**: Carousel slider với customer reviews và star ratings
- **Footer**: Multi-column layout với quick links, social media, newsletter signup

### 1.2 Navigation Header (includes/header.php)
**Component Design:**
- **Logo Area**: Gradient text "Orbix Market" với Pacifico font
- **Main Menu**: Horizontal navigation với dropdown mega-menus
- **Search Component**: Expandable search bar với autocomplete suggestions
- **User Menu**: Avatar dropdown với profile, dashboard, logout options
- **Cart Icon**: Shopping cart với item count badge
- **Mobile Menu**: Hamburger toggle với slide-out navigation panel

### 1.3 Footer Component (includes/footer.php)
**Layout Structure:**
- **Company Info**: Logo, description, contact details
- **Quick Links**: Multi-column grid với categorized navigation
- **Social Media**: Icon grid với hover effects
- **Newsletter**: Email subscription form với call-to-action button

---

## 🔐 **Nhóm 2: Authentication System**

### 2.1 Authentication Page (auth.php)
**UI Mockup Design:**
- **Split Layout**: Hai panel design với form bên trái, branding bên phải
- **Glass Card Effect**: Semi-transparent form container với backdrop blur
- **Tabbed Interface**: Toggle giữa Login/Register/Forgot Password
- **Social Login**: OAuth buttons cho Google, Facebook, Twitter
- **Form Fields**: Styled input fields với floating labels và validation
- **Background**: Animated gradient với floating geometric shapes
- **Email Verification**: Modal popup cho verification code input

### 2.2 Profile Management (profile.php)
**Layout Components:**
- **Profile Header Card**: Large glass card với avatar, user info, badges
- **Statistics Grid**: 4-column grid hiển thị user metrics (templates, orders, etc.)
- **Recent Activity**: Timeline view với activity items và status indicators
- **Quick Actions Sidebar**: Vertical card stack với action buttons
- **Account Summary**: Compact info card với key account details
- **Settings Panels**: Expandable sections cho profile editing

---

## 🛍️ **Nhóm 3: Marketplace & Product Display**

### 3.1 Templates Marketplace (templates.php)
**UI Layout:**
- **Filter Sidebar**: Collapsible panel với category filters, price range, ratings
- **Search & Sort Bar**: Advanced search với multiple sort options
- **Product Grid**: Responsive masonry layout với template preview cards
- **Template Cards**: Hover effects, thumbnail previews, price tags, rating stars
- **Pagination**: Number-based pagination với next/previous controls
- **View Toggle**: Grid/List view switcher buttons

### 3.2 Services Marketplace (services.php)
**Similar Layout với Templates:**
- **Service Cards**: Specialized cards với service pricing tiers
- **Provider Info**: Seller profile integration trong service cards
- **Category Filters**: Service-specific filtering options
- **Featured Services**: Promoted services section at top

### 3.3 Template Detail Page (template-detail.php)
**Detailed View Components:**
- **Hero Section**: Large template preview với image gallery
- **Template Info Panel**: Title, description, specs, pricing
- **Seller Profile Card**: Compact seller info với ratings và contact
- **Action Buttons**: Add to cart, favorite, share buttons
- **Reviews Section**: User reviews với rating breakdown
- **Related Templates**: Horizontal scroll của similar items
- **Preview Modal**: Lightbox cho template images
- **Purchase CTA**: Prominent buy/download button

### 3.4 Service Detail Page (service-detail.php)
**Service-Specific Layout:**
- **Service Overview**: Service description với tier/package options
- **Pricing Tiers**: Tabbed interface cho different service levels
- **Portfolio Gallery**: Work samples từ service provider
- **FAQ Section**: Expandable Q&A về service
- **Order Form**: Service customization options và booking

---

## 🛒 **Nhóm 4: E-commerce Flow**

### 4.1 Shopping Cart (cart.php)
**Cart Interface:**
- **Cart Items List**: Expandable item cards với quantity controls
- **Price Breakdown**: Subtotal, tax, total với clear typography
- **Coupon Section**: Promo code input với apply button
- **Continue Shopping**: Link back to marketplace
- **Checkout CTA**: Large prominent checkout button
- **Empty Cart State**: Friendly illustration với suggested actions

### 4.2 Checkout Process (checkout.php)
**Multi-Step Layout:**
- **Progress Indicator**: Step-by-step checkout progress bar
- **Order Summary**: Fixed sidebar với cart items và pricing
- **Payment Forms**: Credit card, PayPal, other payment method tabs
- **Billing Info**: Auto-filling address forms với validation
- **Order Confirmation**: Success page với order details và next steps

---

## 👨‍💼 **Nhóm 5: Seller Dashboard System**

### 5.1 Seller Channel Hub (seller-channel.php)
**Dashboard Overview:**
- **Navigation Sidebar**: Vertical menu với section icons và labels
- **Main Dashboard**: Dynamic content area với tabbed sections
- **Quick Stats**: Metric cards grid (earnings, orders, reviews)
- **Recent Activity**: Activity feed với real-time updates
- **Performance Charts**: Interactive graphs cho sales data

### 5.2 Seller Overview (sections/seller-overview.php)
**Dashboard Home Components:**
- **Earnings Summary**: Large metric cards với trend indicators
- **Recent Orders**: Table view với order status và actions
- **Performance Metrics**: Charts và graphs cho analytics
- **Quick Actions**: Shortcut buttons cho common tasks

### 5.3 Product Management (sections/seller-templates.php, seller-services.php)
**Product Admin Interface:**
- **Product Grid**: Admin view với edit/delete actions
- **Add Product Modal**: Multi-step product creation form
- **Bulk Actions**: Select multiple items với batch operations
- **Status Indicators**: Visual status badges (approved, pending, rejected)
- **Upload Interface**: Drag-drop file upload với progress bars

### 5.4 Order Management (sections/seller-orders.php)
**Order Processing UI:**
- **Order Table**: Sortable table với order details
- **Order Detail Modal**: Expandable view với customer info
- **Status Management**: Dropdown để update order status
- **Communication Tools**: Message threads với customers

### 5.5 Analytics & Reports (sections/seller-analytics.php)
**Data Visualization:**
- **Revenue Charts**: Interactive line/bar charts
- **Performance Metrics**: KPI dashboard với colored indicators
- **Traffic Analytics**: User behavior data với heatmaps
- **Export Tools**: PDF/Excel export buttons

### 5.6 Earnings & Payments (sections/seller-earnings.php)
**Financial Dashboard:**
- **Balance Overview**: Available balance với withdrawal options
- **Transaction History**: Detailed payment records table
- **Payout Methods**: Payment method management interface
- **Tax Documents**: Downloadable tax forms và receipts

### 5.7 Reviews & Ratings (sections/seller-reviews.php)
**Feedback Management:**
- **Review Cards**: Customer review display với ratings
- **Response Interface**: Reply to reviews functionality
- **Rating Breakdown**: Visual rating distribution charts
- **Review Analytics**: Sentiment analysis và trends

### 5.8 Messages & Communication (sections/seller-messages.php)
**Messaging System:**
- **Conversation List**: Chat thread sidebar với unread indicators
- **Message Interface**: Real-time chat với file sharing
- **Customer Profiles**: Quick access to customer information
- **Automated Responses**: Template message options

---

## 🎛️ **Nhóm 6: Modal Components & Interactions**

### 6.1 Product Management Modals
**Add Product Modal (modals/add-product-modal.php):**
- **Multi-step Form**: Wizard-style product creation
- **File Upload**: Drag-drop interface với preview
- **Category Selection**: Dropdown với search functionality
- **Pricing Setup**: Price input với commission calculator

**Edit Product Modal (modals/edit-product-modal.php):**
- **Inline Editing**: Quick edit functionality
- **Version Control**: Track changes và revision history
- **Bulk Edit**: Update multiple products simultaneously

### 6.2 Communication Modals
**Message Modal (modals/message-modal.php):**
- **Compose Interface**: Rich text editor với formatting
- **Recipient Selection**: User search với autocomplete
- **Attachment Support**: File upload với size limits

**Notification Modal (modals/notification-modal.php):**
- **Alert System**: Categorized notifications với action buttons
- **Settings Panel**: Notification preferences management

### 6.3 Business Modals
**Order Details Modal (modals/order-details-modal.php):**
- **Comprehensive View**: Complete order information display
- **Action Buttons**: Process, refund, communicate options
- **Timeline**: Order status progression tracker

**Promotion Modal (modals/promote-modal.php):**
- **Marketing Tools**: Product promotion campaign setup
- **Budget Management**: Advertising spend controls
- **Performance Tracking**: Campaign analytics preview

---

## 🎨 **Design System & UI Components**

### Color Palette:
- **Primary**: `#FF5F1D` (Orange)
- **Secondary**: `#1f2937` (Dark Gray)
- **Glass Effects**: `rgba(255,255,255,0.1)` với backdrop blur
- **Success**: Green variants
- **Warning**: Yellow/Orange variants
- **Error**: Red variants

### Typography:
- **Headings**: Pacifico font cho branding
- **Body Text**: Inter font family
- **UI Elements**: Various weights (300-700)

### Interactive Elements:
- **Buttons**: Glass morphism style với hover transitions
- **Cards**: Semi-transparent backgrounds với rounded corners
- **Forms**: Floating labels với validation states
- **Navigation**: Smooth animations và state transitions

### Responsive Design:
- **Mobile First**: Progressive enhancement từ 320px
- **Tablet**: Optimized layout cho 768px+
- **Desktop**: Full features cho 1024px+
- **Large Screens**: Enhanced experience cho 1440px+

---

## 🔧 **Technical UI Framework**

### Frontend Stack:
- **CSS Framework**: Tailwind CSS 3.4.16
- **Icons**: Remix Icons 4.6.0
- **Fonts**: Google Fonts (Inter + Pacifico)
- **Animations**: CSS3 transitions và keyframes
- **JavaScript**: Vanilla JS với modern ES6+ features

### UI Architecture:
- **Component-Based**: Reusable PHP includes
- **Modular CSS**: Utility-first với custom components
- **Responsive Grid**: CSS Grid và Flexbox
- **Mobile Optimization**: Touch-friendly interfaces

Thiết kế này tạo nên một marketplace professional với user experience mượt mà, visual hierarchy rõ ràng và functionality hoàn chỉnh cho cả buyers và sellers.
