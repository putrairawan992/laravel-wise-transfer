# Page Design Specification (Desktop-first)

## Global Styles (all pages)
- Layout approach: Hybrid CSS Grid + Flexbox.
  - App shell uses CSS Grid (sidebar + main column).
  - Topbar and cards use Flexbox for alignment.
- Bootstrap usage: Prefer Bootstrap utilities for spacing/typography, and components for nav, cards, tables, forms, modals.
- Design tokens
  - Background: `#f5f7fb` (app), `#ffffff` (cards)
  - Primary: `#0d6efd` (Bootstrap primary)
  - Text: `#0f172a` (primary), `#64748b` (muted)
  - Borders: `#e5e7eb`
  - Radius: 12px for cards, 8px for inputs/buttons
  - Shadows: subtle `0 6px 18px rgba(15, 23, 42, 0.08)` for elevated cards
- Typography
  - Base: 14–16px system stack
  - Headings: 20–28px, semibold
  - Table text: 13–14px
- Button states
  - Primary hover: darken primary by ~6–8%
  - Disabled: reduce opacity and disable pointer events
  - Loading: show spinner + “Processing…” label
- Link styles
  - Primary link color = primary
  - Underline on hover only
- Responsive behavior
  - Desktop-first: persistent sidebar ≥ 992px.
  - Below 992px: sidebar collapses to offcanvas; topbar shows hamburger.

---

## 1) Login Page

### Layout
- Centered card layout using Flexbox (full viewport height).
- Max width: 420–480px.

### Meta Information
- Title: "Login — Transfer Dashboard"
- Description: "Sign in to manage transfers and send money securely."
- Open Graph: title/description consistent with above, type "website".

### Page Structure
1. Background (app background color)
2. Login card (white card with shadow)

### Sections & Components
- Brand block
  - Product name/logo (text-based is fine)
  - Short subtitle: “Secure transfers dashboard”
- Login form
  - Email input (type=email)
  - Password input (type=password)
  - Primary CTA button: “Sign in”
  - Inline validation messages under fields
  - Form-level error alert (Bootstrap alert-danger) for auth failures
- Footer links (optional for MVP)
  - “Forgot password?” (only if you implement it; otherwise omit)

### Interaction states
- On submit: disable inputs + show button spinner
- On error: focus first invalid field

---

## 2) Dashboard Page

### Layout
- App shell grid:
  - Column 1: Sidebar (260px)
  - Column 2: Main content (fluid)
- Main content uses stacked sections with consistent spacing (24px).

### Meta Information
- Title: "Dashboard — Transfer Dashboard"
- Description: "Overview of your balance and recent transfers."
- Open Graph: title/description consistent with above.

### Page Structure
1. Sidebar (left)
2. Main area
   - Topbar
   - Content container
     - Page header
     - Summary cards row
     - Recent transfers table card
     - Transfer details modal/panel

### Sections & Components
- Sidebar
  - Brand/header area (logo + name)
  - Navigation list
    - Dashboard (active)
    - Send Money
  - Secondary area
    - Divider
    - Logout
  - Active state: left indicator bar + bold label
- Topbar
  - Left: hamburger button (visible on <992px)
  - Right: user dropdown
    - Email/name
    - Logout action
- Page header
  - Title: “Dashboard”
  - Subtitle: short contextual line (“Your latest activity and balance.”)
- Summary cards row (Bootstrap cards)
  - Card 1: Available balance
  - Card 2: Currency
  - Card 3: Transfers this week (optional if you compute it; otherwise omit)
- Recent transfers table (within a card)
  - Table columns: Date, Recipient (masked), Amount, Status, Action
  - Status badges: pending (warning), completed (success), failed (danger)
  - Action: “View” button opens details modal/panel
- Transfer details modal/panel
  - Header: Transfer ID + status badge
  - Body: Date, Amount, Recipient name, Recipient masked account, Note (only if present)
  - Security note (small text): “Sensitive details are masked for safety.”

### Interaction states
- Table row hover highlight
- “View” opens modal; modal closes with ESC/backdrop

---

## 3) Send Money Page (Multi-step)

### Layout
- Uses the same app shell (sidebar + topbar) as Dashboard.
- Content uses a two-column layout on desktop:
  - Left (primary): Step form card
  - Right (secondary): Live summary card (sticky on desktop)

### Meta Information
- Title: "Send Money — Transfer Dashboard"
- Description: "Create a new transfer in three steps: details, review, confirm."
- Open Graph: title/description consistent with above.

### Page Structure
1. Top-level header
   - Title: “Send Money”
   - Step indicator
2. Main content grid
   - Step card (dynamic)
   - Summary card

### Sections & Components
- Step indicator
  - Horizontal stepper (Bootstrap nav/segmented)
  - Steps: 1) Details, 2) Review, 3) Receipt
- Step 1: Details (form)
  - Recipient name (text)
  - Recipient account/identifier (text)
    - Helper text: “We will store this encrypted; only masked values are shown.”
  - Amount (number with min > 0)
  - Currency (select; default from account)
  - Note (textarea, optional)
  - Buttons
    - Secondary: “Cancel” (returns to Dashboard)
    - Primary: “Review”
- Step 2: Review (read-only)
  - Read-only summary list with masked recipient account
  - Buttons
    - Secondary: “Back”
    - Primary: “Confirm & Send”
- Step 3: Receipt (success state)
  - Confirmation banner (Bootstrap alert-success)
  - Receipt card
    - Transfer ID
    - Status
    - Amount + currency
    - Recipient name + masked account
    - Created time
  - Buttons
    - Primary: “Back to Dashboard”

### Live summary card (right column)
- Displays current inputs in a compact form
- Masks recipient account immediately after entry (e.g., “•••• 1234”)
- Shows fees line only if you implement fees; otherwise omit

### Interaction + safety states
- Prevent double submit on Confirm (disable button + show spinner)
- If validation fails, keep user on the same step and show errors
- Never render plaintext recipient account after submission; always use masked display fields
