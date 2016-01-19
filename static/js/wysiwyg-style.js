CKEDITOR.stylesSet.add('default',
    [
        // Block-level styles
        {name: 'Heading 2', element: 'h2'},
        // Block-level styles

        {name: 'List LeitnerLeitner', element: 'ul', attributes: {'class': 'list-ll'}},

        {name: 'Table', element: 'table', attributes: {'class': 'table table-borderless'}},

        {name: 'Text Muted', element: 'p', attributes: {'class': 'text-muted'}},
        {name: 'Text Primary', element: 'p', attributes: {'class': 'text-primary'}},
        {name: 'Text Success', element: 'p', attributes: {'class': 'text-success'}},
        {name: 'Text Info', element: 'p', attributes: {'class': 'text-info'}},
        {name: 'Text Warning', element: 'p', attributes: {'class': 'text-warning'}},
        {name: 'Text Danger', element: 'p', attributes: {'class': 'text-danger'}},

        {name: 'Background Primary', element: 'p', attributes: {'class': 'bg-primary'}},
        {name: 'Background Success', element: 'p', attributes: {'class': 'bg-success'}},
        {name: 'Background Info', element: 'p', attributes: {'class': 'bg-info'}},
        {name: 'Background Warning', element: 'p', attributes: {'class': 'bg-warning'}},
        {name: 'Background Danger', element: 'p', attributes: {'class': 'bg-danger'}},

        {name: 'Visible Extra Small Devices', attributes: {'class': 'visible-xs'}},
        {name: 'Visible Small Devices', attributes: {'class': 'visible-sm'}},
        {name: 'Visible Meidum Devices', attributes: {'class': 'visible-md'}},
        {name: 'Visible Large Devices', attributes: {'class': 'visible-lg'}},

        {name: 'Hidden Extra Small Devices', element: '*', attributes: {'class': 'hidden-xs'}},
        {name: 'Hidden Small Devices', element: '*', attributes: {'class': 'hidden-sm'}},
        {name: 'Hidden Meidum Devices', element: '*', attributes: {'class': 'hidden-md'}},
        {name: 'Hidden Large Devices', element: '*', attributes: {'class': 'hidden-lg'}},

        {name: 'Label Default', element: 'span', attributes: {'class': 'label label-default'}},
        {name: 'Label Primary', element: 'span', attributes: {'class': 'label label-primary'}},
        {name: 'Label Success', element: 'span', attributes: {'class': 'label label-success'}},
        {name: 'Label Info', element: 'span', attributes: {'class': 'label label-info'}},
        {name: 'Label Warning', element: 'span', attributes: {'class': 'label label-warning'}},
        {name: 'Label Danger', element: 'span', attributes: {'class': 'label label-danger'}},

        {name: 'Badge', element: 'span', attributes: {'class': 'badge'}},

        {name: 'Alert Success', element: 'div', attributes: {'class': 'alert alert-success'}},
        {name: 'Alert Info', element: 'div', attributes: {'class': 'alert alert-info'}},
        {name: 'Alert Warning', element: 'div', attributes: {'class': 'alert alert-warning'}},
        {name: 'Alert Danger', element: 'div', attributes: {'class': 'alert alert-danger'}},

        {name: 'List Group', element: 'ul', attributes: {'class': 'list-group'}},
        {name: 'List Group Item', element: 'li', attributes: {'class': 'list-group-item'}},
        {name: 'List Group Item Success', element: 'li', attributes: {'class': 'list-group-item-success'}},
        {name: 'List Group Item Info', element: 'li', attributes: {'class': 'list-group-item-info'}},
        {name: 'List Group Item Warning', element: 'li', attributes: {'class': 'list-group-item-warning'}},
        {name: 'List Group Item Danger', element: 'li', attributes: {'class': 'list-group-item-danger'}},

        {name: 'Linkable List Group', element: 'div', attributes: {'class': 'list-group'}},
        {name: 'Linkable List Group Item', element: 'a', attributes: {'class': 'list-group-item'}},
        {name: 'Linkable List Group Item Success', element: 'a', attributes: {'class': 'list-group-item-success'}},
        {name: 'Linkable List Group Item Info', element: 'a', attributes: {'class': 'list-group-item-info'}},
        {name: 'Linkable List Group Item Warning', element: 'a', attributes: {'class': 'list-group-item-warning'}},
        {name: 'Linkable List Group Item Danger', element: 'a', attributes: {'class': 'list-group-item-danger'}},

        {name: 'List Group Heading', element: 'h4', attributes: {'class': 'list-group-item-heading'}},
        {name: 'List Group Text', element: 'p', attributes: {'class': 'list-group-item-text'}},

        {name: 'Panel', element: 'div', attributes: {'class': 'panel panel-default'}},
        {name: 'Panel Body', element: 'div', attributes: {'class': 'panel-body'}},
        {name: 'Panel Primary', element: 'div', attributes: {'class': 'panel panel-primary'}},
        {name: 'Panel Success', element: 'div', attributes: {'class': 'panel panel-success'}},
        {name: 'Panel Info', element: 'div', attributes: {'class': 'panel panel-info'}},
        {name: 'Panel Warning', element: 'div', attributes: {'class': 'panel panel-warning'}},
        {name: 'Panel Danger', element: 'div', attributes: {'class': 'panel panel-danger'}},

        {name: 'Panel Heading', element: 'div', attributes: {'class': 'panel-heading'}},
        {name: 'Panel Heading Title', element: 'h3', attributes: {'class': 'panel-title'}},
        {name: 'Panel Footer', element: 'div', attributes: {'class': 'panel-footer'}},

        {name: 'Well', element: 'div', attributes: {'class': 'well'}},
        {name: 'Well Large', element: 'div', attributes: {'class': 'well well-lg'}},
        {name: 'Well Small', element: 'div', attributes: {'class': 'well well-sm'}}
    ]);


CKEDITOR.editorConfig = function (config) {
    config.styleSet = "default";
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
};
