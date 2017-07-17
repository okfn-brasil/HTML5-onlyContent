HTML5-onlyContent
=================

&nbsp; *(under construction)*

HTML with only content, definition and conversion tools.

## Motivation ##

Many online tools with a "HTML upload" interface, offline softwares with "document loading", input modules of [Content Management Systems](https://en.wikipedia.org/wiki/Content_management_system),
of [Document Management Systems (DMSs and old EDMs)](https://en.wikipedia.org/wiki/Document_management_system)... They supposed that user is sending full-text content in a whole HTML document (only relevant content are into the tag `body`). This is  because HTML is the "lingua franca" and the best way to do *content-interchange*.

So, in that context, HTML must be interpreted as "only content" (no text is about menus, sidebars, forms, Javascript, etc.). In that conetxt arises a HTML's DTD variant that can be named "SIMPLIFIED  HTML FOR NON-INTERACTIVE CONTENT", and for short, *HTML-OnlyContent*.

## The specification  ##

The **HTML5-onlyContent** is a content tag suite for XML or HTML formats, used to describe an HTML format that can be used as "content container" in databases or technical and legal literature published online. Its tag set (and attibutes) is a subset of HTML5 tag set, preserving same HTML5 DTD, strucuture and semantic rules.

See **[spec.md](spec.md)**.  For rationale see [notes.md](notes.md).

## Tools ##

Some transformation tools, compatible with the definition, are available. See ex. the simple XSLT `onlyContent-filter.xsl`.

Another usual (complex) task in this context is to transform all the CSS, and all `class` attributes, into `style` attributes (see. ex. [CssToInlineStyles](https://github.com/tijsverkoyen/CssToInlineStyles) project); where the use of old `font` and `center`  tags, as well normalization of bolds and italics, can be accomplished.
