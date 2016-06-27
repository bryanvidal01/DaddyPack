(function(){
    tinymce.create('tinymce.plugins.clrzformats', {
        init : function(ed, url){
            ed.addCommand('mceClrzFormat1', function(){
                tinyMCE.activeEditor.selection.setContent(
                    '<div class="post-format1">'+
                    '<div class="col1">'+
                    '<p>The world needs dreamers and the world needs doers. But above all, the world needs dreamers who do - Sarah Ban Breathnach. </p>'+
                    '<p>Everyone who has ever taken a shower has had an idea. It’s the person who gets out of the shower, dries off, and does something about it that makes a difference - Nolan Bushnell. </p>'+
                    '</div>'+
                    '<div class="col2"><img src="http://placekitten.com/300/130" alt="" /></div>'+
                    '<div class="col3"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi. Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue. Ut in risus volutpat libero pharetra tempor. Cras vestibulum bibendum augue. Praesent egestas leo in pede. Duis arcu massa, scelerisque vitae, consequat in.</p></div>'+
                    '</div>'+
                    '<p>Lorem !</p>');
            });
            var theme_uri = document.getElementById('clrz-syntax-template-uri').value;
            ed.addButton('ClrzFormat1', {
                title: 'Add two Columns',
                image:theme_uri+'/images/admin/icn-col2.png',
                cmd: 'mceClrzFormat1'
            });
        },
        createControl : function(n, cm){
            return null;
        },
        getInfo : function(){
            return {
               longname: 'Colorz',
                author: '@Colorz',
                authorurl: 'http://www.colorz.fr/',
                infourl: 'http://twitter.com/Colorz',
                version: "1.0"
            };
        }
    });
    tinymce.PluginManager.add('clrzformats', tinymce.plugins.clrzformats);
})();