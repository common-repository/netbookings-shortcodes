(function (wp) {
    var logo = wp.element.createElement('img', {
        width: 26,
        height: 26,
        src: '/wp-content/plugins/netbookings-shortcodes/editor-integration/images/nb.gif'
    });

    var NetbookingsButton = function (props) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, {
                icon: logo,
                title: 'Insert a Netbookings shortcode',
                onClick: function () {

                },
            }
        );
    }
    wp.richText.registerFormatType(
        'netbookings/button', {
            title: 'Insert a Netbookings shortcode',
            tagName: 'netbookings',
            className: null,
            edit: NetbookingsButton,
        }
    );
})(window.wp);