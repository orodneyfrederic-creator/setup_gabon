# Design system: SETUP-Gabon

Recorded from the shipped build (November 2026). Product truth is in `PRODUCT.md`. The tokens live in one file, `resources/views/layouts/partials/theme.blade.php`, which is included by `layouts.app` and `layouts.auth`. Never redefine colours elsewhere.

## Principles

1. **The logo leads.** Every colour is derived from the logo: the royal-blue wordmark and the green and yellow of its power symbol (Gabon's colours).
2. **Facts, not decoration.** Address, opening hours, WhatsApp, stock and price are always close at hand. No gradients, glassmorphism, gradient text, eyebrows above headings or pulsing badges.
3. **One primary action per screen**, in blue. Secondary actions are outlined or plain links.
4. **Mobile first** for the storefront. Screens are checked at 390 px and 1440 px.

## Colour

The Tailwind config remaps the palette names, so the usual classes (`blue-700`, `emerald-600`…) produce the brand colours.

| Role | Classes | Value | Use |
|---|---|---|---|
| Brand / primary | `blue-700` (hover `blue-800`) | `#283888`, the logo blue | Primary buttons, info bar, active states, links |
| Brand tints | `blue-50` / `blue-100` | `#eef1fa` / `#dde3f4` | Active nav background, soft buttons |
| Confirmation | `emerald-*` | Gabon green `#137a42` (600) | In stock, payment confirmation, "Encaisser" |
| Signal | `amber-*` | Gabon yellow `#f7c91a` (400) | Negotiation, low stock, cart counter |
| Danger | `rose-*` | Tailwind rose | Errors, out of stock, destructive actions |
| Neutrals | `slate-*` / `gray-*` | Cool neutral scale (`#151b2b` → `#f6f7fa`) | Text, borders, page background `slate-50` |

`violet`, `indigo`, `purple`, `sky`, `cyan`, `teal` and `fuchsia` are all mapped to the brand blue, so a rainbow of icons cannot come back.

## Typography

- **Face:** Figtree (Google Fonts, 400–800). Its round geometry echoes the logo's lettering.
- **Weights:** `font-black` is capped at 800. Headings use `font-bold` or `font-extrabold` with `tracking-tight`.
- **Scale:**
  - page title `text-2xl sm:text-3xl`;
  - section title `text-lg`/`font-bold`;
  - body `text-[15px]`–`text-base`;
  - helper text `text-xs`/`text-sm` in `slate-500`;
  - nothing smaller than 11 px.
- **Prices and quantities:** always use the `.chiffres` class (tabular figures). Write "FCFA", never "XAF".

## Shape and depth

- **Radius:** `lg` 8 px for buttons and inputs, `xl` 10 px for cards, `2xl` 12 px for large panels, `3xl` 16 px at most.
- **Shadows:** soft and offset (`shadow-sm` → `shadow-2xl`, redefined in the config). Use them only on overlays (cart panel, menus, modals) and on card hover.
- **Borders:** `border-slate-200` on white cards over a `slate-50` background.

## Components

- **Pagination:** Laravel's Tailwind paginator, translated in `lang/fr/pagination.php` and `lang/fr.json`. `darkMode: 'class'` keeps its `dark:` variants from following the phone's dark mode.
- **Buttons:**
  - primary: `h-11`/`h-12 px-5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-semibold`;
  - secondary: `border border-slate-300` or `border-blue-700 text-blue-700`.
- **Fields:** `h-12 px-3.5 border-slate-300 rounded-lg focus:border-blue-600 focus:ring-4 focus:ring-blue-100`, with the label above in `text-sm font-semibold` and the error below in `text-rose-600 text-sm`.
- **Status badges:** use the `account.partials.statut` partial (en attente = amber, confirmée = blue, livrée = green, annulée = slate).
- **Product card:** 4:5 image with `object-cover`, category, two-line name, price, stock line with a coloured dot, and a soft "Ajouter" button.
- **Notifications:** white card, coloured icon, top right (full width on mobile); errors stay on screen for 9 s.

## Signature

A tricolour hairline (green, yellow, blue in thirds) appears at the top of the footer and of the auth pages. It is the only decorative motif: no rings, blobs or ornamental shapes.

## Layouts

- **Storefront:**
  - blue info bar (address, hours, WhatsApp);
  - white sticky header (menu, logo, search, nav, account, cart);
  - `max-w-7xl` content;
  - white footer.
- **Back office (gérante):**
  - a fixed `w-64` left nav on desktop (a drawer on mobile), grouped into Ventes, Catalogue and Clients & documents, with the active state in `blue-50`;
  - a full-width header with no storefront search or cart;
  - no footer;
  - page title only (sentence case, no icon), sentence-case table headers, state pills only for rows that need action;
  - the dashboard puts the « À traiter » items first, not a strip of vanity counters.

## Imagery

Use real photos only: the shop (`public/img/boutique-setup.jpg`, which leads the home hero and the About page) and product photos uploaded by the gérante. The recommended product shot is on a plain background, well lit and centred (this guidance appears in the product form).
