Pico SyntaxHighlighter
======================

Adds automatically [SyntaxHighlighter](http://alexgorbatchev.com/SyntaxHighlighter/) scripts and themes to your pages.

SyntaxHighlighter was originally created in 2004 and is still maintained by Alex Gorbatchev and is released under both MIT License and GPL License.

You can download SyntaxHighlighter and read the documentation at : [http://alexgorbatchev.com/SyntaxHighlighter/](http://alexgorbatchev.com/SyntaxHighlighter/).

The code of SyntaxHighlighter is available on GitHub : [https://github.com/alexgorbatchev/SyntaxHighlighter](https://github.com/alexgorbatchev/SyntaxHighlighter).

## Installation

Place `syntaxhighlighter` folder into the `plugins` directory.

## Usage

### Configuration

Everything happens inside the `config.php` file.

````
// Select the theme for SyntaxHighlighter, from the syntaxhighlighter/styles folder :
// Default, Django, Eclipse, Emacs, FadeToGrey, MDUltra, Midnight, RDark
$config['synhigh']['theme'] = 'Emacs';
// If "true", load the Autoloader script that dynamically load the brushes
// without having to load them all on the same page
// If true, you have to define the brushes you use on your pages
$config['synhigh']['autoloader'] = true;
// If you use the autoloader, you have to define the brushes you use on your pages
// or all of them. The brushes can be found in the syntaxhighlighter/scripts folder. 
// You can specify your own aliases for the brushes.
$config['synhigh']['autobrush'] = array(
	'bash shell' => 'Bash',
	'txt plain text' => 'Plain',
	'js jscript javascript' => 'JScript',
	'tex latex' => 'Latex',
	'php' => 'Php',
	'xml xhtml xslt html' => 'Xml'
);
// If you don't use the autoloader function, you have to declare here all the brushes you use
// on your pages ; all brushes declared here will be loaded on each page of your site.
// See the syntaxhighlighter/scripts folder
$config['synhigh']['brush'] = array(
	'Bash',
	'Plain',
	'JScript',
	'Latex',
	'Php',
	'Xml'
);
// Don't load SyntaxHighlighter on certain pages based on __*single*__ meta tags,
// i.e. meta tags that take only one value, like title, template, category...
// Use the pipe | as separator, without space ; case-sensitive.
$config['synhigh']['exclude'] = array(
	'template' => 'category',
	'title' => 'GCweb, qu\'est-ce que c\'est ?|CV',
	'category' => 'Maîtrise Sciences du langage|Master recherche Sciences du langage|Master professionnel Édition'
);
````

### Content

In your content pages, you can use this code for example :
````
<pre class="brush:php">
'GCbooks' => array(
  'numeric' => array('id','rank','pages','rating'),
  'date' => array('publication','added','acquisition'),
  'string' => array('isbn','title','edition','description','comments','translator','artist'),
  'bool' => array('read'),
  'list' => array('authors','publisher','language','serie','format','genre','location'),
  'image' => array('cover','backpic'),
  'unknow' => array(''),
  'url' => array('web')
),
</pre>
````

If you are using the autoloader function and if you have specified your own alias, you can use both of these :
````
<pre class="brush:txt">
hihihi
</pre>
<pre class="brush:plain">
hihihi
</pre>
````

If you are loading the brushes the "normal way", you have to use the aliases defined in each brush file. For the plain text brush, two aliases are defined : text and plain, but not txt.

#### Using a snippet with Sublime Text

Instead of typing the `pre` code each time, you can use a snippet like this one:

```
<snippet>
	<content><![CDATA[
<pre class="brush:${1:lang}">

</pre>
]]></content>
	<!-- Optional: Set a tabTrigger to define how to trigger the snippet -->
	<tabTrigger>sxh</tabTrigger> -->
	<!-- Optional: Set a scope to limit where the snippet will trigger -->
	<!-- <scope>source.python</scope> -->
</snippet>
```