# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Primary:** private individuals in Libreville (and across Gabon) shopping for computers, printers and IT accessories, mostly on a phone. They browse the catalogue, compare prices, sometimes negotiate, then finalise the order over WhatsApp.
- **Secondary:** businesses and professionals asking for quotes (devis), usually handled by the shop.
- **Staff:** the gérante runs the back office (products, stock, online orders, negotiations, POS, quotes, invoices, after-sales) on a desktop at the shop.

## Product Purpose

SETUP-Gabon is the online storefront and back office of an IT equipment retailer in Gabon. Customers find a product, see its real price and stock, and send a complete order to the shop via WhatsApp. The shop confirms, delivers, and tracks stock and revenue in one place.

## Positioning

A local shop, not a marketplace: real stock in Libreville, prices in FCFA, an order that lands directly in the shop's WhatsApp, and price negotiation built into the product page (within limits set by the gérante).

## Operating Context

- Customers: mobile data, small screens, outdoor light; WhatsApp is the communication channel (+241 66 13 12 70).
- Orders: statuses en attente, confirmée, livrée, annulée; payment in cash or by mobile money outside the site (no online payment yet).
- Delivery is free in Libreville (confirmed by the owner, 2026-11-07); pickup at the shop is also possible.
- Back office: a single gérante account; POS sales at the counter; PDF quotes and invoices.

## Capabilities and Constraints

- Laravel 12 + Blade, Tailwind via CDN, Alpine.js via CDN, Font Awesome icons. No build step required for views.
- Cart stored in the browser (localStorage).
- One image (plus an optional video) per product; no reviews, no filters by brand/price yet.
- No legal pages yet (CGV, mentions légales, confidentialité).

## Brand Commitments

- Name: SETUP-Gabon. Logo: `public/img/logo.png`, royal blue wordmark with a green-and-yellow power symbol (Gabon's colours).
- **Blue stays the dominant brand colour** (user requirement). Green and yellow from the logo may be used as functional accents (green = confirmation/in stock, yellow = signal/negotiation).
- The user wants the site to look professional, not "AI-generated" or amateur.
- Language: French.

## Evidence on Hand

- Logo (`public/img/logo.png`), one background photo (`public/img/font_app.jpg`), product photos uploaded by the gérante.
- Brands carried (from the About page): HP, Dell, Lenovo, Apple, Canon.
- No testimonials, customer counts, ratings or delivery-time guarantees exist: do not invent them.

## Product Principles

1. The price, the stock and the next step are always visible: a customer never wonders how to buy.
2. Mobile first: every customer flow works one-handed on a 360-px screen.
3. Trust through facts (real stock, real shop, real WhatsApp contact), never through invented claims.
4. The back office favours speed and clarity over decoration.
