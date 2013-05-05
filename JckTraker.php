<?php

/**
 * Cette classe permet de sécuriser le debuggage PHP dans vos scripts (locaux
 * et distant).
 *
 * @author Stéphane Bouvry
 */
class JckTraker {

    ////////////////////////////////////////////////////////////////////////////
    // CONFIGURATION 
    // You can add your local, or remote IP but for debug step only.
    public static $allow_IP = array('::1', '127.0.0.1');

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////////////////////////////
    // Constantes
    const INFO      = 1;
    const SUCCESS   = 2;
    const WARNING   = 3;
    const ERROR     = 4;
    const DATABASE  = 5;
    
    ////////////////////////////////////////////////////////////////////////////
    // Static properties
    private static $instance;
    
    ////////////////////////////////////////////////////////////////////////////
    // Instance property
    private $OUTPUT         = "";
    private $TRAC_NUM       = 0;
    private $debug          = false;
    protected $timer;
    protected $delay;
    
    /**
     * Equivalent à un var_dump mais en version sécurisé.
     *
     * @author  Jacksay<studio@jacksay.com>
     * @version 1.0
     */
    public static function debug($mixedvar, $comment = '') {
        self::getInstance()->OUTPUT .= '<pre><strong>' . $comment . '</strong>' . htmlentities(print_r($mixedvar, true)) . "</pre>\n";
        self::getInstance()->TRAC_NUM++;
    }

    /**
     * Affiche une entrée dans les logs de type $type (par défaut une INFO).
     * 
     * @param mixedvar $message Information à afficher
     * @param int $type Type d'information.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 1.0
     */
    public static function flow($message, $type = self::INFO) {
        self::getInstance()->OUTPUT .= '<p class="jcktraker-flow-' . $type . '">' . htmlentities($message) . "</p>\n";
        self::getInstance()->TRAC_NUM++;
    }

    ///////////////////////////////////////////////////////// SHORTCUT
    /**
     * Utilisée pour loguer les informations générales.
     * 
     * @param mixed $message L'information à afficher.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 2.0
     */
    public static function info($message) {
        self::flow($message);
    }
    
    /**
     * Utilisée pour loguer les erreurs.
     * 
     * @param mixed $message L'information à afficher.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 2.0
     */
    public static function error($message) {
        self::flow($message, self::ERROR);
    }
    
    /**
     * Utilisée pour loguer les avertissements.
     * 
     * @param mixed $message L'information à afficher.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 2.0
     */
    public static function warning($message) {
        self::flow($message, self::WARNING);
    }
    
    /**
     * Utilisée pour loguer les opérations réussies.
     * 
     * @param mixed $message L'information à afficher.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 2.0
     */
    public static function success($message) {
        self::flow($message, self::SUCCESS);
    }
    
    /**
     * Utilisée pour loguer les opérations de base de données.
     * 
     * @param mixed $message Les informations à afficher.
     * 
     * @author  Jacksay<studio@jacksay.com>
     * @version 2.0
     */
    public static function database($message) {
        self::flow($message, self::DATABASE);
    }

    /**
     * Cette méthode est automatiquement appellée lorsque vous importer le fichier
     * JckTraker.php dans votre script.
     *
     * @author  Jacksay<studio@jacksay.com>
     * @version 1.0
     */
    public static function init() {
        if (in_array($_SERVER['REMOTE_ADDR'], self::$allow_IP)) {
            self::getInstance()->timer = microtime(true);
            self::getInstance()->debug = true;
            error_reporting(E_ALL);
        } else {
            self::getInstance()->debug = false;
            error_reporting(0);
        }
    }

    /**
     * Accésseur
     *
     * @author  Jacksay<studio@jacksay.com>
     * @version 1.0
     */
    private static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new JckTraker();
        }
        return self::$instance;
    }

    /**
     * Element clef, va afficher la barre de debug dans votre page.
     *
     * @author  Jacksay<studio@jacksay.com>
     * @version 1.0
     */
    function __destruct() {
        if (!$this->debug)
            return;
        $this->delay = floor( (microtime(true) - $this->timer)*1000) . "ms";
        ?>
        <!-- JCK TRAKER BOX v1.0 -->
        <script type="text/javascript">
        function jcktraker_hide() {
            var sections = document.getElementsByName('jcktraker-section');
            var num_sections = sections.length;
            for (var i = 0; i < num_sections; i++) {
                sections[i].style.display = 'none';
            }
        }
        function jcktraker_close(){
            localStorage.setItem("jcktraker-own", "off");
            jcktraker_hide();
        }
        function jcktraker_show(section) {
        console.log(section);
            if( document.getElementById(section).style.display == 'block' ){
               jcktraker_hide();
            }
            else {
               jcktraker_hide();
               document.getElementById(section).style.display = 'block';
            }
            
            if( section == "jcktraker-own" ){
                console.log("auto show ON");
                localStorage.setItem("jcktraker-own", "on");
            } else {
                console.log("auto show OFF");
                localStorage.setItem("jcktraker-own", "off");
            }
        }
        
        function initStore( varName, elementId ){
            var isCheck = localStorage.getItem(varName);
            var elem = document.getElementById(elementId);
            var data = varName;
            elem.checked = (isCheck === "true") ? true:false;
            elem.addEventListener("change", function(){
                localStorage.setItem(data, this.checked);
                updateStoredVisibility(this.id);
            });
            updateStoredVisibility(elementId);
        }
    
        function updateStoredVisibility(id){
            var elems = document.getElementsByClassName("jcktraker-"+id);
            var visible = document.getElementById(id).checked;
            for( i=0; i<elems.length; i++ ){
                elems[i].style.display = visible ? "block" : "none";
            }
        }
        
        window.onload = function(){
            if( localStorage ){
                initStore("info",       "flow-1");
                initStore("success",    "flow-2");
                initStore("warning",    "flow-3");
                initStore("error",      "flow-4");
                initStore("database",   "flow-5");
                if( localStorage.getItem("jcktraker-own") === "on" ){
                    jcktraker_show('jcktraker-own');
                }
            }
            window.addEventListener('keydown', function(e){
               if( e.keyCode == 27 ){
                  jcktraker_hide();
               }
            });
            
        };
        </script>
        <style type="text/css">
            #jcktraker-box * {
               margin: 0;
               padding: 0;
               font-size: 10px;
               text-align: left;
               font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            }
            #jcktraker-box {
                position: fixed;
                -moz-transition: ease .3s all;
                bottom: 0;
                right: 0;
                left: 0;
                max-height: 90%;
                max-width: 75%;
                background: rgba(255,255,255,.1);
                /*box-shadow: 0 0 8px rgba(0,0,0,.3);*/
                padding-bottom: 20px;
            }
            #jcktraker-box:hover {
                background-color: rgba(255,255,255,.9);
            }
            #jcktraker-menu:hover {
               right: 0;
            }
            #jcktraker-menu > li > strong {
               display: block;
               line-height: 20px;
            }
            #jcktraker-menu {
               width: 75px;
               height: 250px;
                position: fixed;
                bottom: -230px;
                right: 0;
                -moz-transition: ease-in .3s all;
                transition: ease-in .2s all;
                line-height: 14px;
                padding: 0;
                padding-left: 20px;
                margin: 0;
                /*float: right;*/
                border-radius: 1em 0 0 0;
                background: #000;
                background: rgba(200,200,200,.75);
                text-shadow: -1px 1px 2px rgba(0,0,0,.3), 1px -1px 2px rgba(255,255,255,.3);
                color: #fff;
                background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAArhJREFUeNqUU19IU1Ec/jY3ZLrN1JBCAyUfphBaQpCYUGkPitmDFEoUWPZSGf55EXsLM0QopbdW9KBEEGQoFkX1YBY41BSRpS3TXd0Y2+62O7e73etO59w226SXPjjnd8/5fffjO7/zOyr8A3WNTWNiMNAoiiKi0QjEiGxeWpxt28sjhEDNPm51dJPkRDiW3njhynXUNjShyFSObTntWnI+ma9hk+AP7CYHz6BXEkZdfvNonobSSlXA8YNwqfPR2/UefXv5Kjadb7pIYjE1qrzPrbn7YSou0MKgI7sEQVRh1S7B44b1c06zSa2OYezlCxU7guJgbW29p86w0F914oCpIH8f5B12PjriAll0KiwGOI438V9fYVIo70lxQJE9UA1ve9clxPTZkIMOECn016Y2AxpdLuSwBPPwU3R+QA7d5hUHl1vbrkaRbo6tPgI3MQIxcx+MpaehyyuGhvmLAQGHHV7ra2xzTkgS0Nx606tFhBX2iUav15t5ITJOxRqMZ2/jcNlRzD8bhLxsQZizI7voECRtFo51PMSPT1+Ax8NQkdi43qA3MwHmsIKOwv6TID+H6ok0N0jm+moI2Rghb89BiZa7NWRnfoDM3Ksn96uV0hSy/xJFnGUq6jRabYcNwlYu/Js2RO3fEJahRN5uQ4BbBM/ZFB7Fr/j400hKQ6ghOJ1OBB3LCPIeKmZFhN5GkEbB50HQaYXb5VR4yU2lCJyqrW+x4AhZ2vBR4iqikoyQ1wWJFjDEuxCJyPBw37Hl9WGG8hg/RcCQqRv1GcuMDyyonpzyi2t8CO6NBezQS3avL2AzGMKbKUEconmfoczI+CkCRqMB3PpKCydguvsdKq0+3JmYjqw4qYNxGlfouucjKlme8Rg/BTfaO1llM5K2WKOUxG+oJL5OICPOV15jAhX4P1QkBH4LMADYK1S5qnGrYAAAAABJRU5ErkJggg==) no-repeat 2px 2px rgba(200,200,200,.75);
            }
            #jcktraker-box pre{
                margin: 10px;
                border: solid #999 1px;
                color: #000;
                padding: 4px;
                box-shadow: inset -2px 2px 2px rgba(0,0,0,.3);
                overflow: hidden;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
            #jcktraker-own pre {
                background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAArhJREFUeNqUU19IU1Ec/jY3ZLrN1JBCAyUfphBaQpCYUGkPitmDFEoUWPZSGf55EXsLM0QopbdW9KBEEGQoFkX1YBY41BSRpS3TXd0Y2+62O7e73etO59w226SXPjjnd8/5fffjO7/zOyr8A3WNTWNiMNAoiiKi0QjEiGxeWpxt28sjhEDNPm51dJPkRDiW3njhynXUNjShyFSObTntWnI+ma9hk+AP7CYHz6BXEkZdfvNonobSSlXA8YNwqfPR2/UefXv5Kjadb7pIYjE1qrzPrbn7YSou0MKgI7sEQVRh1S7B44b1c06zSa2OYezlCxU7guJgbW29p86w0F914oCpIH8f5B12PjriAll0KiwGOI438V9fYVIo70lxQJE9UA1ve9clxPTZkIMOECn016Y2AxpdLuSwBPPwU3R+QA7d5hUHl1vbrkaRbo6tPgI3MQIxcx+MpaehyyuGhvmLAQGHHV7ra2xzTkgS0Nx606tFhBX2iUav15t5ITJOxRqMZ2/jcNlRzD8bhLxsQZizI7voECRtFo51PMSPT1+Ax8NQkdi43qA3MwHmsIKOwv6TID+H6ok0N0jm+moI2Rghb89BiZa7NWRnfoDM3Ksn96uV0hSy/xJFnGUq6jRabYcNwlYu/Js2RO3fEJahRN5uQ4BbBM/ZFB7Fr/j400hKQ6ghOJ1OBB3LCPIeKmZFhN5GkEbB50HQaYXb5VR4yU2lCJyqrW+x4AhZ2vBR4iqikoyQ1wWJFjDEuxCJyPBw37Hl9WGG8hg/RcCQqRv1GcuMDyyonpzyi2t8CO6NBezQS3avL2AzGMKbKUEconmfoczI+CkCRqMB3PpKCydguvsdKq0+3JmYjqw4qYNxGlfouucjKlme8Rg/BTfaO1llM5K2WKOUxG+oJL5OICPOV15jAhX4P1QkBH4LMADYK1S5qnGrYAAAAABJRU5ErkJggg==) no-repeat 2px 2px;
                padding: 2px;
            }
            #jcktraker-own pre > strong {
                display: block;
                padding-left: 20px;
                line-height: 16px;
            }
            #jcktraker-own p:nth-child(odd) { background-color: #eee; }
            #jcktraker-own p:nth-child(even) { background-color: #ddd; }
            p[class^="jcktraker-flow-"]{
                line-height: 18px;
                padding-left: 20px;
                background-repeat: no-repeat;
                background-position: 0;
                border-bottom: dotted thin #999;
                font-family: "Courier New", monospaced;
            }
            .jcktraker-flow-1{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAp5JREFUeNpkU01IVFEU/t6972fG1JHGrLBso2WRSAshonIjFrZQ27QoEQza6K6gXYS0KKhWuijIjbZw0xhRoEagUhRWUIJSWuL4A44OzDjhjOO89zrnzjwZ68Jh5pzvO+d857x7NfxzSq+Fem3b7XBd1+eSr7FpWkpKrW99oLUzn0schauzr22o0dC04ca6cpyrKUMw4CMGJwPr8RQmpiIYmVzGtuteWOtvGdlVgJP3B6zhW1dqEEvZeDcTxczKH2xuOQj4JY4dLER99V6U+CQeDk5hNb6linABwQV06tzdfgqf5hN4Nr6EudUkuluqsPS4Hjap+L2WRN/EssKZx3xPuc4zd1ysxNivOL6E4yj060hnHAiRnc5nSewxpfrPuGkKMP+JE+qlUKdwHOd6+YEAvi4kiGjAZ+jK7r2ex8k7H+E3dVjk8y/jzGM+52XVQ7PC8TT8hlBdaSxkSPf3u6dV17P3P8OUWhYj33BcMJ/zGBe85XgqA8vSYRoSBhtJHpyMqAJCl9BzccaZx3wt9/1YAQVski1Ud2pAi3OhS7VfmLpQJnM7yTa0WYFXwE07tmNaNB/NlS1AoJdg5jp7vhACmQwpoDzlO6no0MpKDAW0bR+TVUdtRyLPSLdQxRlnHvM5D7mbWlF69cVCU8MJFBVZ2KZP2FQdQHNtcOfKtg/MqmSDiiQSW3jzdhrrzy8foYsU1gkPZxbfd42OyZ5LDcdRTEVG5xJ4NR1Tl4iao9hv0BgCG5Q8OvYDzOc8TwGf4pLzN9vkoTM9dbWHcbSyDEUFJrzHlNhM4+dcBJPfFmEvfeiKjT/qp/DGrsfERciqgq1Pb2v+YDMt0/QA2l/aTUZfRkM3HpA7y8n/vca8U0HGCxB5MYcs6snOf85/BRgAU2QTxLlxCL0AAAAASUVORK5CYII=)}
            .jcktraker-flow-2{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAe1JREFUeNqck81rE1EUxX/vTdKCCBYFQaOCH+iiWLWKIAguWoIU3Qguin+BCLqwtG5U0IWgdVVdajeCG2nBojbdFQUtahQaFCGY4GRI/GjTjiRpTGbGO5kIqba08cJhLm/mnHveO/OU53mMXjuab3VCbYPrJmnxuGtA/9M+iqxQPlf7jeu6bXsO7GdtGM53XT23df3OQtcNYsdvEl1JJBQogeN6KAX7Irvo2HwFc/5HdDwxGtWDL4qOy0CsnzvLCtQN1YTsgsXL5CV2bDrF6cPdnOjsXjOVfD/UcvvZULlafhg2GHjSh7mEgKo5KP3KUanCp9wIyZkRNm44ROfuvRxpP8uHTLr3+fTr3p5blhUyuCykYd1oR/gUylm0ND48wVf7DYncMKn5R2zbUuTMyQ7at0ci4vb+Xw4IHJQswoaBqx20xKHlYCvyLpHK8OVbBisrh15iwjYZXyQgG6hNXahkaJWNlnCEAGZe8F1EfhLLpxl794BX8rmsMsu9RgdCrroyKWuSyjuYcwFpLsVYPCDNCmYE9r8pKElAUFiAiWnnsf2ZmEyaWo60ZIyOcrhoH6Pn+uSFOtFmFRX8SL4ALmXlHxdpmqhFMVaVS7Ol/+SvtK53/yMg5I/xuDybF1D+lVRKHWxYe7tass/9LcAAIcPHKZaPykAAAAAASUVORK5CYII=)}
            .jcktraker-flow-3{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAcFJREFUeNqkUz1LI1EUPe9NkkGTiI1FFHGVIBEUg8vaRHGLFC6sRcDCZtlyQbCw8C9YWIpaWYgWgoX7A9zWIoXWimhgkZUt1LDiB5mZ9/adlw8GGURI4PBOzjn35d6bGaG1Rjuf2MnqVKShlBp0RO2KPNCJISllJSono0StFAKvdpidKYEgp/auCziSDvyl7t4P+a6xaRDk1KLGlRG/3imc+Hpu7jsqZ5cW5NTovXkB21S+dzxQ+Aq4/7CyeWpBTo3e61FkqHeowF9IZ7L5nk+fgastCJMlyKnRYwahUWRo6+abu58rLQLna4B3i5daYEFOzXomo0JdyFDrR/3T85D3h8DzbyCRxsOTZ0FOjR4zzDZHidVbD4qpTK6YGekCKmUgnqSDg+WbxtPSYTRzVsvIjIzj7ixXfPx7UXSE+CW1MvPIjqPhL7PAza5ZWMrAFLgufuyMWpDXtZTN2KypYa1USm/0TRYQ936aVpOtYiRc3N9WLchbl5gMs6xhrThZLeiJb72NTYr6ycNh641t+0YIuKzm5urkdO8PYoGvUd6+hh1FN6t5mPYalBa0aP7fNiKMKR1h0x/beRtFu6/zfwEGANJw3nxTDnFtAAAAAElFTkSuQmCC)}
            .jcktraker-flow-4{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAn5JREFUeNpkU01IVUEU/ub+vaflDz0pbJEuCsyoiKRAKRdSQRtbhJugqKCNtoygXcv2ui0o2hSRbQwjISNFy59Q8ocK1OBJ4uv5Xpp63313Ot9cb1gNzD33nPN935k5M6Pwz+hvrO/SYXhNa52MY0qpDWVZ95sHp9q3YwUDFTtvmw6dhev07j3ZhN3HG+BVVAiCbMDP5bA0OoL08ABQCM6dHvj06i8Bkr1UVe/By1dhr2Twa7APhZlJhOtrsEp2wK07jNLGFhQrU5h++AB+ZtmIUCCq3nxUr718plfu3NCLZ+p0+lSNLrx7rTlo6TPOPHHExytwuOfa1osoDvXBn/xgKupiCKepxYjTWm4CSuIm7zogvr9Q6JJ0uxUC13elKhFMjMJOlsJJJGHLDOY+GwFa+iYueeKIJ495S2bC/vYVSgCW58FyXAGWIPg4HAmIpc8488QRT14koERjJQvbFbJNAU8qisDESCQglj7jzBNHvOFxi3LIULms2edWZ8zU40PInz8mFROwdpYL0oXB8mRzWfNvVqAV/EA+tidLdBypYkOFRSSu3ER5z7ix9BlnnjjiyTMCy37QvZRflQKJaBvcq2XDu3DJVKClb3ojeeKIJ88ItA1P35pPf8dmIEfnRSLsut/92AjQ0jdkyRNHPHlRD4CFN5l8hz0129lwpB4lySS0bUH3PkfY8xSOLW0qK4cSgfWNDYxNzYJ48mIB3J1eeGQ6PjbRub+2BtXVe+CkyqKGSrOCzU0sStUvc/Po//GzI8Yjeip/hrQaB56cqLtd5TqtwvbixyQff7kQvGh7P3NPHN6w/H+vcdvYJzO1dcniIRcPmXjZ25/zbwEGAGFgG+lMOaZnAAAAAElFTkSuQmCC)}

            .jcktraker-flow-5{ background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAk5JREFUeNpsU8tu00AUPeNXQh7NA6qKBAqBDYI1LBBCYlMWCJAQZVMWgMQHAN9B+gFIwAKQUNkA6oYlQkgQaIElBBBSiRqwnYfTh+143DuT2CQRk5zYc+ecO/eezLAwDMEYw4Ol58fp/Q7hPCGF/wzibRJeEu5en79Yk1rx8/DZi1u6rlfLpX0o5PLITmUh4hNiOF0HrU4bvxtr8H3/9rXLFxY1scg5rx49ckwS+0EAu9UGwsnt5Re5qZzE6ueVKk0XFbEmdqvsLyGfyyJhaFQWF2lpYRxiTXAqs6W4QlkBUxS8q31AqVzCnmIRmUxKEiKSKF98eptbsCwb3+p10rCRBEQIQ4b19T9E6MAwDCQTCaR3pcAph+tuYdv14Pdd0Tsi4/8lIChUhaapEqqqyDkntTJ8FzEeqsIvBDyQFcUJWra13La756and6NYKKKQz0vRKELywHF6sG0bpmWS2d5ynEDXDXfhyjy+1n8QoYW1RoMq0aCruuy1T2X7QZ9aSuLwoYM4O3cGT54uuXGCdCZzqbbyCeXyXlQOzNJ80kQBBb2NgYkfV79ITZxAtMODEI1GE6bZhi5MNMjEFJnIBya6ngfPH5gYiOBwKAM9k65G/arDZxBwerIxLySPsfhfUKJMHDwmRFDY+DzaBCPHXCb49fN71bQsdHuOLFFTNWniGCjm0VrH6aJp/pUaWf3wUMws3Lg5d+LkqauZ7NRpiidDcRnC8ctEY7vndF+/f/vm0eP7916RtslGTlWaMCPuy2hrE0O41yE0CRtCuyPAACZBGVgAMt/bAAAAAElFTkSuQmCC)}


            #jcktraker-box p{
                margin: 0 10px;
                padding-left: 20px;
            }
            ul#jcktraker-menu li {
                display: block;
            }

            #jcktraker-box div[name="jcktraker-section"] {
                  position: fixed;
                  bottom: 0;
                  display: none;
                  overflow: scroll;
                  width: 75%;
                  max-height: 75%;
                  opacity: .8;
                  padding: 0;
                  background: #fff;
                  box-shadow: 0 0 .6em rgba(0,0,0,.3);
                  text-align: left;
            }
            #jcktraker-box div[name="jcktraker-section"] > a {
               /*background: #ff6600;
               float: right;
               clear: both;
               position: fixed;
               top: 25%;
               left: 75%;
               display: block;
               height: 3%;*/
               display: none;
            }
            
            #jcktraker-box div[name="jcktraker-section"]:hover {
                opacity: 1;
            }

            ul#jcktraker-menu:hover {
                bottom: 0;
                background-color: #555;
            }

            #jcktraker-menu a {
                -moz-transition: ease-in .3s all;
                transition: ease-in .3s all;
                color: #f60;
                background: inherit;
                text-decoration: none;
                padding: 0 .3em;
            }

            #jcktraker-menu a:hover {
                color: #000;
                background: #E50;
                text-decoration: none;
            }


        </style>
        <div id="jcktraker-box">

            <div id="jcktraker-post" name="jcktraker-section">
               <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php htmlspecialchars(print_r($_POST)); ?></pre>
            </div>
            <div id="jcktraker-get" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php
        $dt = print_r($_GET, true);
        echo htmlspecialchars($dt);
        ?>
                </pre>
            </div>
            <div id="jcktraker-server" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php
        $dt = print_r($_SERVER, true);
        echo htmlspecialchars($dt);
        ?>
                </pre>
            </div>
            <div id="jcktraker-session" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php
        $dt = isset($_SEESION)?print_r($_SESSION, true):'No session started';
        echo htmlspecialchars($dt);
        ?>
                </pre>
            </div>
            <div id="jcktraker-cookie" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php
        $dt = print_r($_COOKIE, true);
        echo htmlspecialchars($dt);
        ?>
                </pre>
            </div>
            <div id="jcktraker-request" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
                <pre><?php
        $dt = print_r($_REQUEST, true);
        echo htmlspecialchars($dt);
        ?>
                </pre>
            </div>
            <div id="jcktraker-own" name="jcktraker-section">
            <a href="javascript:jcktraker_hide()">close</a>
        <?php echo $this->OUTPUT; ?>
            </div>
            <ul id="jcktraker-menu">
                <li id="jckheader"><strong>JckTraker v1.0</strong></li>
                <li><a href="javascript:jcktraker_show('jcktraker-own'); void(0)" >TRAC(<?php echo $this->TRAC_NUM ?>)</a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-post')" >$_POST(<?php echo count($_POST) ?>)</a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-get')" >$_GET(<?php echo count($_GET) ?>)</a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-server')" >$_SERVER(<?php echo count($_SERVER) ?>)</a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-session')" ><?php
                if (isset($_SESSION)) {
                    echo '$_SESSION(', count($_SESSION), ')';
                } else {
                    echo '<del>$_SESSION</del>';
                }
                ?></a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-cookie')" >$_COOKIE(<?php echo count($_COOKIE) ?>)</a></li>
                <li><a href="javascript:jcktraker_show('jcktraker-request')" >$_REQUEST(<?php echo count($_REQUEST) ?>)</a></li>
                 <li><div>exec in <strong><?php echo $this->delay; ?></strong></div></li>
                <li><a href="javascript:jcktraker_close();" class="iconified-close">Fermer</a></li>
                <li>
                    <ul>
                       
                        <li>Visibility :</li>
                        <li><input type="checkbox" id="flow-1" checked="checked" /><label for="flow-1">infos</label></a></li>
                        <li><input type="checkbox" id="flow-2" checked="checked" /><label for="flow-2">success</label></a></li>
                        <li><input type="checkbox" id="flow-3" checked="checked" /><label for="flow-3">warning</label></a></li>
                        <li><input type="checkbox" id="flow-4" checked="checked" /><label for="flow-4">error</label></a></li>
                        <li><input type="checkbox" id="flow-5" checked="checked" /><label for="flow-5">database</label></a></li>
                    </ul>  
                </li>
            </ul>

        </div>
        
        <?php
    }

}

JckTraker::init();

function debug($mixedvar, $comment = "Debug") {
    JckTraker::debug($mixedvar, $comment);
}

function info($mixedvar) {
    JckTraker::info($mixedvar);
}

function success($mixedvar) {
    JckTraker::success($mixedvar);
}

function warning($mixedvar) {
    JckTraker::warning($mixedvar);
}

function error($mixedvar) {
    JckTraker::error($mixedvar);
}

function database($mixedvar) {
    JckTraker::database($mixedvar);
}
?>
