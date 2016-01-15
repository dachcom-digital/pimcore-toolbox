var pimcore = pimcore || {};

pimcore.registerNS("pimcore.document.tags.wysiwyg");

if( pimcore.document.tags.wysiwyg !== undefined ) {

    pimcore.document.tags.wysiwyg = Ext.extend(pimcore.document.tags.wysiwyg, {

        /**
         *
         * http://ckeditor.com/latest/samples/toolbarconfigurator/index.html#basic
         */
        startCKeditor: function () {

            this.options["toolbarGroups"] = [

                { name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph' ] },
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                '/',
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                { name: 'links', groups: [ 'links' ] },
                { name: 'insert', groups: [ 'insert' ] },
                { name: 'forms', groups: [ 'forms' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others', groups: [ 'others' ] },
                { name: 'about', groups: [ 'about' ] }

            ];

            this.options["removeButtons"] = 'Save,NewPage,Preview,Print,Templates,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Outdent,Indent,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Image,Flash,Smiley,PageBreak,Iframe,FontSize,TextColor,BGColor,Maximize,About,Strike,Font';

            this.callParent(arguments);

        }

    });

}