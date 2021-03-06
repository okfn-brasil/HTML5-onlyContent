# Notes and rationale

## HTML5 references

* "Palpable" elements as [DOM/palpable-content](https://www.w3.org/TR/html5/dom.html#palpable-content-0): all HTML5-onlyContent tags are palpable.

* Template: *"The template element is used to declare fragments of HTML that can be cloned and inserted in the document by script"*, so, as script-dependent, `template` tag is not in the HTML5-onlyContent tag set.

## xhtml-print selection

Tags selected from [xhtml-print 2010](https://www.w3.org/TR/xhtml-print/), that was an XHTML1 and HTML4 based standad.

> The XHTML-Print document type is defined as a set of XHTML modules. All XHTML modules are defined in the Modularization of XHTML specification [XHTMLMOD].

Selected modules by HTML5-onlyContent as reference-model:

* Structure Module: `body`, `head`, `html`, `title`.

* Text Module: `abbr`, `acronym`, `address`, `blockquote`, `br`, `cite`, `code`, `dfn`, `div`, `em`, `h1`, `h2`, `h3`, `h4`, `h5`, `h6`, `kbd`, `p`, `pre`, `q`, `samp`, `span`, `strong`, `var`.

* Hypertext Module:  `a`

* List Module: `dl`, `dt`, `dd`, `ol`, `ul`, `li`.

* Text Extension Module - Presentation: `b`, `big`, `hr`, `i`, `small`, `sub`, `sup`, `tt`

* Basic Tables Module: `caption`, `table`, `td`, `th`, `tr`

* Image Module: `img`

* Metainformation Module: `meta`

* Base Module: `base`


## The label tag for labeling

The  [HTML5/the-label-element](https://www.w3.org/TR/html5/forms.html#the-label-element)  specification declares *"The label element represents a caption"*, so, there are contradiction in the use of `<label>` for labeling. 

When in no interface context, the `label` remains as a structural part, with the semantic of [aria-label](https://www.w3.org/TR/wai-aria/#aria-label) and the [presentation](https://www.w3.org/TR/wai-aria/#presentation) role. 
  
See also [W3C's Nu Validator](https://validator.w3.org/nu/), that accept non-form context use, and Mozilla's guide, content [categories for `label`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/label): *"flow content, phrasing content, ..."*. Some discussion at [SO](https://StackOverflow.com/a/32408312/287948).

## Removed tags

See tags `form`, `nav`, `script`, `style`, etc.

------

## The auditable metadata problem  
The `meta` tag (and similar methods as JSON-LD or hidden-Microdata) is not the best way to present metadata, because it is not expressed as content, so it is not auditable by usual reader of the content. 

As a "[convention over configuration](https://en.wikipedia.org/wiki/Convention_over_configuration) strategy", we can adopt a tag or an attribute to play the rule of "non-content/metadata section".

The primary use of `nav`  tag is for navigation interface,  but there are no contradiction and is a good practice to encapsulate "non-official content", so the `nav`  can be used as metadata section in a HTML5-onlyContent context. As usual `nav` blocks must be deleted, to avoid confusion, the convention for differentiation is the use of the attribute `class="content-metadata"`, and at least one `itemprop` in.  Example:

```html
<article itemscope itemprop="Legislation" itemtype="http://schema.org/Legislation">
  <nav class="content-metadata"> <!-- non-official content -->
    <dl itemprop="isPartOf" itemscope itemtype="http://schema.org/PublicationIssue">
      <dt>Published in</dt> <dd><time itemprop="dateCreated" datetime="2017-11-30">30/11/2017</time></dd>
      <dt>Issue</dt>       <dd itemprop="issueNumber" itemprop="identifier">229</dd>
    </dl>
    <dl>
      <dt>Jurisdiction</dt>  <dd itemprop="legislationJurisdiction" value="br">Federal</dd>
      <dt>Authority</dt>     <dd itemprop="legislationPassedBy">Ministry of Foreign Affairs</dd>
    </dl>
  </nav>
  ...  official content of the article ...
</article>
```

### The need for human-readable source-code

HTML, XML or XHTML5 formats, they **are not human-readable** without basic "pretty format" applyed to its source codes. 

There are a lot of "pretty HTML" libraries, but no one is simple and based on C14N standard. 

There are a nedd for (reliable and) simple algorithm based in regular expression, 
and a simple toolkit to simplify the expression of the "pretty rules"
of a convention.
 
