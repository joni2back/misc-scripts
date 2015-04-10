// vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Templating/Helper/TranslatorHelper.php
    /**
     * @see TranslatorInterface::trans()
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        $str = $this->translator->trans($id, $parameters, $domain, $locale);
        return "<span data-catalog=$domain   class=trjonas>$str</span>";
    }

setInterval(function() {
    document.oncontextmenu = function() {return false;};
    $('.trjonas').css('outline', '1px dashed red');
    $('.trjonas').unbind('contextmenu');
    $('.trjonas').bind('contextmenu', function() {
        var oriobj = $(this);
        var ori = oriobj.html();
        var catalog = $(this).attr('data-catalog');
        var xxx = prompt('Nueva traduccion para:     ' + ori, ori);
            var theData = {"params": {
               old: ori,
               new: xxx,
               catalog: catalog
            }};

            $.postJSON("/translation/fix", theData, function(data) {

                data.result && data.result.translation && require(['notifications'],function(n){
                    n.success('Traduccion realizada para: ' + ori);
                    oriobj.hide(500);
                    oriobj.html(data.result.translation);
                    oriobj.show(500);
                });

            }).always(function() {

            });
    });
}, 300);
