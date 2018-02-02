<?php
/**
 * Converte STDIN ou URI (de HTML livre) em XHTML normalizado, padrão C14N, para fins de preservação digital e checksum.
 * Exemplos de uso com terminal na raiz do projeto:
 *   php src/htm_normalize.php  < teste.htm | more
 *   php src/htm_normalize.php  https://www.w3.org/TR/cooluris/  > cooluris.std.htm
 */

// IO

$VERBOSE_ERRORS = true;
if ($argc<2) $url = 'php://stdin';
else $url = $argv[1];
if (!$url) die("\nERRO: sem input definido\n");

$html = file_get_contents($url);


$tidy_config = [ // see http://api.html-tidy.org/tidy/quickref_5.4.0.html
    'indent'=>'auto'   //not working
   ,'output-xhtml'=>1,    'wrap'=>0,   'drop-empty-elements'=>'yes'
   ,'add-xml-decl'=>1,         'output-xml'=>1,      'output-encoding'=>'utf8'
   ,'preserve-entities'=>'no', 'quote-nbsp'=>'no'
   ,'break-before-br'=>'no',   'indent-spaces'=>2   // deu pau com 'markup'=>'yes'
   ,'hide-comments'=>'yes' // not working
   //'sort-attributes'=>'none'  -- check https://www.w3.org/TR/xml-c14n2
]; // cuidados: output-xml faz ignorar  show-body-only; ...


$tidy = new tidy;  // requer apt install php7.0-tidy
$tidy->parseString($html, $tidy_config, 'utf8');
$tidy->cleanRepair();
$xml = (string) $tidy;

// Process:
$dom = new DOMDocument;
$dom->loadXML( $xml ); // XHTML

print cleanXML($dom,true);


// // // // // //
// // // LIB

/**
 * Converte DOMDocument livre em DOMDocument normalizado, limpando espaços e ordenando atributos.
 * @param $dom DOMDocument
 * @output string com XML "limpo".
 */
function cleanXML($dom,$cutElements=false) {
	if ($cutElements)
		nonOnly_strip($dom); //when no bugs, $dom = nonOnly_transf($dom);
	$dom->normalizeDocument();   // secondary DOM normalization

	$xC14N = $dom->documentElement->C14N(); // main, normalize attribute order, etc.
	$dom2 = new DOMDocument;
	$dom2->preserveWhiteSpace = false; 	// before load
	$dom2->formatOutput = true;      	// any
	$dom2->loadXML($xC14N);
	$dom2->formatOutput = true;
	$dom2->encoding = 'UTF-8';      	// after load
	return $dom2->saveXML(); // documentElement
}


/**
 * Secure remove of "non-OnlyContent HTML tags".
 */
function nonOnly_strip(&$dom) {
	$queue = [];
	$stripList  = 'processing-instruction()|comment()'; // copy from XSLT
	$stripList .= '|script|form|iframe|object|menu|menuitem|noscript|option|textarea|input';
	$stripList .= '|canvas|datalist|details|keygen|optgroup|progress';
	$stripList .= '|applet|frame|frameset|noframes';
	$stripList = explode('|',$stripList); // 21 items
	foreach($stripList as $ename)
		foreach($dom->getElementsByTagName($ename) as $e)
			{$e->nodeValue=''; $queue[]=$e;} // secure removing
	foreach($queue as $e) $e->parentNode->removeChild($e);
}

/**
 * Sometimes a form, etc. need only to remove tags but not its contents 
 */
function stripNonOnlyTags($html) {
	return strip_tags($html, '<base>, <body>, <head>, <html>, <meta>, <title>, <address>, <article>, <aside>, <section>, <footer>, <header>, <dl>, <dt>, <dd>, <ol>, <ul>, <li>, <blockquote>, <br>, <div>, <h1>, <h2>, <h3>, <h4>, <h5>, <h6>, <label>, <p>, <pre>, <a>, <abbr>, <bdi>, <bdo>, <br>, <cite>, <code>, <data>, <dfn>, <em>, <kbd>, <mark>, <q>, <rtc>, <samp>, <small>, <span>, <strong>, <sub>, <sup>, <time>, <var>, <wbr>, <b>, <big>, <hr>, <i>, <rp>, <rt>, <ruby>, <small>, <s>, <sub>, <sup>, <u>, <figure>, <img>, <svg>, <table>, <tbody>, <tfoot>, <thead>, <td>, <th>, <tr>, <col>, <colgroup>');
}

/**
 * Ideal the control by XSLT... But it is not working fine, with any HTML. 
 */
function nonOnly_transf($dom) {
	$domXsl = new DOMDocument;
	$domXsl->load('src/onlyContent-filter.xsl');
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($domXsl);
	return $proc->transformToXml($dom);
}

/* Old

  // $dom->loadHTMLfile($url,LIBXML_NOWARNING); // not yet an HTML_PARSE_NOWARNING
  libxml_use_internal_errors(true);
  $dom->loadHTMLfile($url);
  libxml_clear_errors();

   Lembretes e justificaiva para usar Tidy e evitar loadHtml no DOMDocument.
   DOMDocument tem suas falhas no loadHtml, principalmente por não saber ler HTML5 até hoje....
   .. não tem check DOCTYOPE and <meta charset=utf-8"> para HTML5
   precisa simular HTML4 trocando por <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   ...
   XSLT também apresentou falhas na remoção de tags como <script>, talvez por não tratar input como XML.
*/

