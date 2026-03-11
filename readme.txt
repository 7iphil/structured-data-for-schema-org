=== Structured Data for Schema.org ===
Contributors: philstudio
Tags: schema, structured-data, json-ld, seo, google-rich-snippets
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://yoomoney.ru/to/4100141266469

Generate Schema.org structured data via shortcode. Supports HowTo, FAQPage, ItemList, CreativeWork.

== Description ==

Structured Data for Schema.org helps you add valid Schema.org markup to your WordPress content. This improves how search engines understand your content and **may qualify for Google Rich Snippets** (depending on schema type and page structure).

= Supported Schema Types =

* **HowTo** — Step-by-step tutorials (Google Rich Result eligible, 1 per page recommended)
* **FAQPage** — Questions & Answers (Google Rich Result eligible, 1 per page limit enforced)
* **ItemList** — Ordered/unordered lists (Google Rich Result eligible)
* **CreativeWork** — General content (SEO only, no Rich Result)

= Important: Google Rich Results =

Not all schema types guarantee Rich Snippets. Google has specific requirements:

* **HowTo:** Best results with ONE main tutorial per page
* **FAQPage:** Only ONE FAQPage schema per page is eligible for Rich Results (plugin enforces this)
* **ItemList:** Works well for top-10 lists, product collections
* **CreativeWork:** Helps SEO but does not trigger Rich Results

This plugin generates **valid Schema.org markup**. Rich Result eligibility depends on your content structure and Google's algorithms.

== Installation ==

= Using the WordPress Plugin Installer =

1. Go to WordPress Dashboard → Plugins → Add New.
2. Search for "Schema Structured Data".
3. Click "Install" then "Activate".
4. Add `[schema]` shortcodes to your posts or pages.

= Manual Installation =

1. Download the plugin zip file.
2. Unzip and upload to `/wp-content/plugins/structured-data-for-schema-org`.
3. Activate via Dashboard → Plugins.
4. You're ready! Add `[schema]` shortcodes to your content.

== Usage ==

= HowTo Schema =

`
[schema type="HowTo" name="How to Draw a Rune" time="5M"]
Step 1: Clear your mind
Step 2: Focus on your question
Step 3: Click the daisy
[/schema]
`

= FAQPage Schema =

Use `|` to separate questions and answers (one Q&A per line):

`
[schema type="FAQPage" name="Rune FAQ"]
What are runes? | Ancient Germanic alphabet symbols
How do I use them? | Focus on a question and draw randomly
When should I draw? | When you need guidance
[/schema]
`

For schema-only output (no visible HTML), add `hidden="1"`:

`
[schema type="FAQPage" name="FAQ" hidden="1"]
Question 1 | Answer 1
Question 2 | Answer 2
[/schema]
`

= ItemList Schema =

`
[schema type="ItemList" name="Top 3 Runes"]
Fehu - Wealth and abundance
Uruz - Strength and vitality
Thurisaz - Protection and power
[/schema]
`

For unordered list (bullets), add `items-tag="ul"`:

`
[schema type="ItemList" name="My List" items-tag="ul"]
Item 1
Item 2
Item 3
[/schema]
`

= CreativeWork Schema =

`
[schema type="CreativeWork" name="My Guide" description="A comprehensive rune guide"]
Your content here...
[/schema]
`

= Attributes =

* `type` — Schema type: HowTo, FAQPage, ItemList, CreativeWork (default: HowTo)
* `name` — Title of the schema (required for all types)
* `description` — Brief description (HowTo, CreativeWork)
* `time` — Duration (HowTo only, auto-converts to ISO 8601: "5M", "1H30M", "PT30M")
* `image` — Featured image URL (HowTo only)
* `hidden` — Hide HTML output, keep schema only (values: 1, true)
* `position` — Starting position for ItemList numbering (ItemList only)
* `url` — URL for list items (ItemList only, applies to all items)
* `items-tag` — HTML tag for ItemList: "ol" (default) or "ul"

== Frequently Asked Questions ==

= Will I get a Google Rich Snippet? =

Schema markup **may** qualify for Rich Snippets, but Google has specific requirements:

* **HowTo:** Use ONE per page for best results
* **FAQPage:** Only ONE per page is eligible (plugin enforces this limit)
* **ItemList:** Works well for ranked lists
* **CreativeWork:** SEO benefit only, no Rich Result

Valid schema improves how Google understands your content, but Rich Results depend on content quality, page structure, and Google's algorithms.

= Can I use multiple shortcodes on one page? =

Yes, but with limitations:

* **HowTo:** Multiple allowed, but Google may only show one as Rich Result
* **FAQPage:** Only the FIRST one is processed (Google limit enforced by plugin)
* **ItemList:** Multiple allowed
* **CreativeWork:** Multiple allowed

= How do I verify the schema is working? =

1. Publish your page
2. Visit [Google Rich Results Test](https://search.google.com/test/rich-results)
3. Enter your page URL
4. Check for detected schema types

For syntax validation, use [Schema Validator](https://validator.schema.org/).

= Why does FAQPage only work once per page? =

Google explicitly states that only **one FAQPage schema per page** is eligible for Rich Results. This plugin enforces that limit to prevent invalid markup and improve your chances of getting a Rich Result.

= What is the Q|A format for FAQPage? =

Each line should contain a question and answer separated by a pipe character (`|`):

`
Question 1 | Answer 1
Question 2 | Answer 2
Question 3 | Answer 3
`

The plugin automatically parses this format and generates proper FAQ schema markup.

= Can I hide the HTML output but keep the schema? =

Yes! Add `hidden="1"` to any shortcode:

`
[schema type="FAQPage" name="FAQ" hidden="1"]
Question | Answer
[/schema]
`

This outputs the JSON-LD schema without visible HTML on the page.

== Screenshots ==

1. Shortcode in Editor — Add [schema] to any post or page
2. Frontend Output — Clean HTML with proper Q&A formatting for FAQPage
3. JSON-LD in Source — Valid Schema.org markup in footer
4. Google Rich Results — Validation passed
5. Schema Validator — Validation passed

== Changelog ==

= 1.0.0 =
* 🎉 Initial release
* ✅ Multi-schema support: HowTo, FAQPage, ItemList, CreativeWork
* ✅ FAQPage limited to 1 per page (Google compliance)
* ✅ Q|A format for FAQ content (simple, no nested shortcodes)
* ✅ ItemList supports ol/ul via `items-tag` attribute
* ✅ `hidden` attribute for schema-only output
* ✅ WordPress Coding Standards compliant

= 1.0.1 =
* [Changed] Change plugin name/slug

= 1.0.2 =
* [Fixed] Fix readme file

= 1.0.3 =
* [Added] Add Plugin Icon

= 1.0.4 =
* [Added] Add Screenshots

= 1.0.5 =
* [Added] Add Files

= 1.0.6 =
* [Deleted] Deleted Assets Files
* [Added] Added readme.txt

= 1.0.7 =
* [Deleted] Deleted README.MD
* [Added] Added assets files

= 1.0.8 =
* [Tested] Test Deploy

= 1.0.9 =
* [Tested] Test Deploy

== Upgrade Notice ==

= 1.0.9 =
Test Deploy

== Developer Notes ==

= Classes =

* `SDFSO_Schema` (`includes/class-sdfso-schema.php`) — Core schema generation logic (static methods)
* `SDFSO_Shortcode` (`includes/class-sdfso-shortcode.php`) — Shortcode registration and rendering

= Supported Schema Types =
`
| Type        | Rich Result  | Limit Per Page | Required Fields         |
|-------------|--------------|----------------|-------------------------|
| HowTo       | Yes          | 1 recommended  | name, step[]            |
| FAQPage     | Yes          | 1 enforced     | mainEntity[]            |
| ItemList    | Yes          | ∞              | name, itemListElement[] |
| CreativeWork| No           | ∞              | name                    |
`
== License ==

This plugin is licensed under GPLv2 or later.

Copyright (C) 2026 philstudio

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.