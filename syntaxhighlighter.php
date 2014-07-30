<?php 
/**
 * Pico SyntaxHighlighter
 *
 * Add SyntaxHighlighter by Alex Gorbatchev
 * http://alexgorbatchev.com/SyntaxHighlighter/
 * to Pico
 *
 * @author Brice Boucard
 * @link https://github.com/bricebou/pico_SyntaxHighlighter
 * @license http://bricebou.mit-license.org/
 */

class SyntaxHighlighter {

	public function config_loaded(&$settings)
	{	
		$this->theme_url = $settings['base_url'] .'/'. basename(THEMES_DIR) .'/'. $settings['theme'];
		if (isset($settings['synhigh']['theme']))
        {
            $this->synhigh_theme = $settings['synhigh']['theme'];
        }

        if (isset($settings['synhigh']['autoloader']) && $settings['synhigh']['autoloader'] === true)
        {
    		$this->synhigh_loader = '<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shAutoloader.js"></script>';
    		if (isset($settings['synhigh']['autobrush']) && is_array($settings['synhigh']['autobrush']))
    		{
    			$synhigh_autobrushscripts = '';
    			$this->synhigh_autobrush = $settings['synhigh']['autobrush'];
    			foreach ($this->synhigh_autobrush as $key => $value)
	            {
	            	$synhigh_autobrushscripts .= '\''.$key.'		'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shBrush'.$value.'.js\','.PHP_EOL;
	            }
	            $this->synhigh_autobrushscripts = rtrim($synhigh_autobrushscripts, ",".PHP_EOL);
    		}
        }
    	elseif((isset($settings['synhigh']['autoloader']) && $settings['synhigh']['autoloader'] === false) || empty($settings['synhigh']['autoloader']))
        {
        	if (isset($settings['synhigh']['brush']) && is_array($settings['synhigh']['brush']))
	        {
	        	$synhigh_brushscripts = '';
	            $this->synhigh_brush = $settings['synhigh']['brush'];
	            foreach ($this->synhigh_brush as $key => $value)
	            {
	            	$synhigh_brushscripts .= '<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shBrush'.$value.'.js"></script>';
	            }
	            $this->synhigh_loader = $synhigh_brushscripts.PHP_EOL.'<script>SyntaxHighlighter.all()</script>';
	        }
        }  
	}

	public function after_render(&$output)
	{
		if (isset($this->synhigh_autobrushscripts) && $this->synhigh_autobrushscripts != '') {
			$synhigh_body = '<script type="text/javascript">
					SyntaxHighlighter.autoloader('.PHP_EOL.
					$this->synhigh_autobrushscripts
					.PHP_EOL.');
					SyntaxHighlighter.all();
		    	</script>';
		    
	    	$output = str_replace('</body>', PHP_EOL.$synhigh_body.'</body>', $output);
		}

		$synhigh_head = '
	<link href="'.$this->theme_url.'/scripts/syntaxhighlighter/styles/shCore.css" rel="stylesheet" type="text/css" />
	<link href="'.$this->theme_url.'/scripts/syntaxhighlighter/styles/shTheme'.$this->synhigh_theme.'.css" rel="stylesheet" type="text/css" />
	<script src="'.$this->theme_url.'/scripts/syntaxhighlighter/scripts/shCore.js"></script>'.PHP_EOL.'	'.$this->synhigh_loader;

		$output = str_replace('</head>', PHP_EOL.$synhigh_head.'</head>', $output);
	}
}

?>