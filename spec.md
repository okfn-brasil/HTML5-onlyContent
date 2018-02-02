# HTML5-onlyContent specification

The **HTML5-onlyContent** is a content tag suite for XML or HTML formats, used to describe an HTML format that can be used as "content container" in databases or technical and legal literature published online. Its tag set (and attibutes) is a subset of HTML5 tag set, preserving same HTML5 DTD, strucuture and semantic rules.

## Dependencies

This specification relies on some other underlying specifications.

Main dependence: [W3C/TR/html5](https://www.w3.org/TR/html5). Main sections:

* [HTML5 flow-content tags](https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_categories#Flow_content)
* [HTML5 fragment](https://www.w3.org/TR/html5/infrastructure.html#documentfragment)
* [HTML5 flow-content](https://www.w3.org/TR/html5/dom.html#flow-content-1).

Rationale and informational dependencies: see [notes.md](notes.md).

## The HTML5-onlyContent tag set

* General for document level: `base`, `body`, `head`, `html`, `meta`, `title`.

* Text and content flow:

   - Structure semantics:  `address`, `article`, `aside`, `main`, `section`, `footer`, `header`.
   - List flow: `dl`, `dt`, `dd`, `ol`, `ul`, `li`.
   - General flow:  `blockquote`, `br`, `div`, `h1`, `h2`, `h3`, `h4`, `h5`, `h6`, `label`, `p`, `pre`.
   - Inline text semantics:  `a`, `abbr`, `bdi`, `bdo`, `br`, `cite`, `code`, `data`, `dfn`, `em`,  `kbd`, `mark`, `q`, `rtc`, `samp`, `small`, `span`, `strong`, `sub`, `sup`, `time`,  `var`, `wbr`.
   - Presentation:   `b`, `big`, `hr`, `i`, `rp`, `rt`, `ruby`, `small`, `s`,  `sub`, `sup`, `u`.

* Figures and tables:

    - `caption`, `figcaption`    
    - `figure`, `img`, `svg`
    - `table`, `tbody`, `tfoot`, `thead`, `td`, `th`, `tr`, `col`, `colgroup`

### Attributes in the tags

* Only content and its semantic: ... no animation, no form, DOM-generator, no layout (CSS, style, etc.), no etc.

* Semantic complement: as Microdata or [HTML+RDFa](https://www.w3.org/TR/html-rdfa/).

### Controled extra-tags

See [notes](notes.md) for `<nav class="content-metadata">`. Context: auditable metadata, when content can't use Microdata to express all auditable metadata.

### CDATA

No [CDATA section](https://en.wikipedia.org/wiki/CDATA) in the content, all UTF-8 and "`&lt;`/`&gt;`/`&amp;` converted" when need to show source code.

## The HTML5-onlyContent rules

Minimal rules about the conditional use of some tags.

* Use `svg` tag only as `figure` or `aside` content.

* Use `nav` tag only as auditable metadata, that is not a concrete part of the content.

* In special cases, when the use of the tag `script` is valid:

  - When is not possible to express data in external file (ex. in a CSV table), to complement images or ilustrations, offering raw data, `<script id="myJson" type="application/json">[[head1,head2],[1,2],[3,4]]</script>`.

  - When metadata can't be expressed by `meta`, HTML+RDFa or Microdata, to express a JSON-LD. Example: `<script id="myJson" type="application/json">{name: 'Foo'}</script>`.

* Interactive elements with printable representation: `summary`.

## Filtering and normalizing

As the XHTML5 format can be analysed by XML, and XHTML have less variations tham HTML5, the convention for "normalized format" is **XHTML5-onlyContent**, the standard format for digital preservation and document-comparison.

The converter or filter need also to be an "standard algoritm" as DOMDocument methods and its implementation in [Libxml2](http://xmlsoft.org). As the HTML-to-XHTML need some stadanrds, convention also adopt [Tidy v5.4+](http://api.html-tidy.org). The  [C14N](https://www.w3.org/TR/xml-c14n/) normalization is only for ensure all details like attribute order.

### Normalization for human-readable source-code

Commom and realiable "Pretty-HTML5" algorithm, as standard reference.

There are a lot of "pretty HTML" libraries, but no one is simple and based on C14N standard. The convertion must be also reliable and easy to reproce in many languages (Javascript, Java, PHP, Python, etc.). Ideal is to use *regular expression* transforms as kernel for specification of the "pretty transforms".

The standard can include a simple library to express the "prettiness" of each style (sub-set of the HTML-OnlyContent standard); 
and a "basic pretty" as default transform.
 

## Conformance

1. For XML environments and validators, adopt [HTML5 polyglot](https://www.w3.org/TR/html-polyglot/) as recomendation.

2. Conformance with [html5/infrastructure/common-microsyntaxes](https://www.w3.org/TR/html5/infrastructure.html#common-microsyntaxes)

3. Conformance with [html5/dom/elements](https://www.w3.org/TR/html5/dom.html#elements) <br/> "Authors must not use elements, attributes, or attribute values for purposes other than their appropriate intended semantic purpose, as doing so prevents software from correctly processing the page".

5. Kinds of content, as *"3.2.4.1.2 Flow content"*, see [flow-content-1](https://www.w3.org/TR/html5/dom.html#flow-content-1).

Mappings and filters:

* Assisted map from CSS:  from italics to `i` or `em` tags, from bold to `b` or `strong`, from monospace to `code` or `pre`, etc. See inline style transform and final CSS inline properties to tags.

* Map from HTML4 (obsolete tags) to HTML5, see [migration](https://www.w3schools.com/html/html5_migration.asp) and [html5/html4 convertions](https://github.com/ppKrauss/html5-to-html4).

* XSLT of this specification.
