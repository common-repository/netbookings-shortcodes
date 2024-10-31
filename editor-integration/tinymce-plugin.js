(function () {
    'use strict';

    tinymce.create('tinymce.plugins.netbookingsShortcodesExtensions', {
        init: function (editor, url) {

            editor.addButton('NetbookingsItemShortcode', {
                title: 'Insert a Netbookings shortcode',
                image: url + '/images/nb.gif',
                onclick: function () {
                    var window = editor.windowManager.open({
                        title: 'Insert Netbookings shortcode',
                        minWidth: 500,
                        body: [{
                                type: 'listbox',
                                name: 'packagecategory',
                                label: 'Package category',
                                minWidth: 75,
                                values: netbookingsPackageCategories,
                                onselect: function () {
                                    var packageSelect = window.find('#package');
                                    var serviceCategeorySelect = window.find('#servicecategory');
                                    var serviceSelect = window.find('#service');
                                    if (this.value() >= 1) {
                                        packageSelect.disabled(true);
                                        serviceCategeorySelect.disabled(true);
                                        serviceSelect.disabled(true);
                                    } else {
                                        packageSelect.disabled(false);
                                        serviceCategeorySelect.disabled(false);
                                        serviceSelect.disabled(false);
                                    }

                                }
                            },
                            {
                                type: 'listbox',
                                name: 'package',
                                label: 'Package',
                                minWidth: 75,
                                values: netbookingsPackages,
                                onSelect: function () {
                                    var packageCategeorySelect = window.find('#packagecategory');
                                    var serviceCategeorySelect = window.find('#servicecategory');
                                    var serviceSelect = window.find('#service');
                                    if (this.value() >= 1) {
                                        packageCategeorySelect.disabled(true);
                                        serviceCategeorySelect.disabled(true);
                                        serviceSelect.disabled(true);
                                    } else {
                                        packageCategeorySelect.disabled(false);
                                        serviceCategeorySelect.disabled(false);
                                        serviceSelect.disabled(false);
                                    }
                                }
                            },
                            {
                                type: 'listbox',
                                name: 'servicecategory',
                                label: 'Service category',
                                minWidth: 75,
                                values: netbookingsServiceCategories,
                                onSelect: function () {
                                    var packageCategeorySelect = window.find('#packagecategory');
                                    var packageSelect = window.find('#package');;
                                    var serviceSelect = window.find('#service');
                                    if (this.value() >= 1) {
                                        packageCategeorySelect.disabled(true);
                                        packageSelect.disabled(true);
                                        serviceSelect.disabled(true);
                                    } else {
                                        packageCategeorySelect.disabled(false);
                                        packageSelect.disabled(false);
                                        serviceSelect.disabled(false);
                                    }
                                }
                            },
                            {
                                type: 'listbox',
                                name: 'service',
                                label: 'Service',
                                minWidth: 75,
                                values: netbookingsServices,
                                onSelect: function () {
                                    var packageCategeorySelect = window.find('#packagecategory');
                                    var packageSelect = window.find('#package');
                                    var serviceCategorySelect = window.find('#servicecategory');
                                    if (this.value() >= 1) {
                                        packageCategeorySelect.disabled(true);
                                        packageSelect.disabled(true);
                                        serviceCategorySelect.disabled(true);
                                    } else {
                                        packageCategeorySelect.disabled(false);
                                        packageSelect.disabled(false);
                                        serviceCategorySelect.disabled(false);
                                    }
                                }
                            },
                            {
                                type: 'listbox',
                                name: 'perrow',
                                label: 'Max per row',
                                minWidth: 75,
                                values: [{
                                        text: '1',
                                        value: '1'
                                    },
                                    {
                                        text: '2',
                                        value: '2'
                                    },
                                    {
                                        text: '3',
                                        value: '3'
                                    },
                                    {
                                        text: '4',
                                        value: '4'
                                    }
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'truncate',
                                label: 'Truncate text',
                                minWidth: 75,
                                values: [{
                                        text: 'No',
                                        value: '0'
                                    },
                                    {
                                        text: 'Yes',
                                        value: '1'
                                    }
                                ]
                            }
                        ],
                        onsubmit: function (e) {
                            var shortcode = '[netbookings-';

                            if (e.data.packagecategory != -1) {
                                shortcode += 'packages category=' + e.data.packagecategory;
                            }

                            if (e.data.package != -1) {
                                shortcode += 'packages item=' + e.data.package;
                            }

                            if (e.data.servicecategory != -1) {
                                shortcode += 'services category=' + e.data.servicecategory;
                            }

                            if (e.data.service != -1) {
                                shortcode += 'services item=' + e.data.service;
                            }

                            if (e.data.perrow) {
                                shortcode += ' perrow=' + e.data.perrow;
                            }

                            if (e.data.truncate) {
                                shortcode += ' truncate=' + e.data.truncate;
                            }

                            shortcode += ']';

                            editor.insertContent(shortcode);
                        }
                    })
                }
            });
        }
    });

    tinymce.PluginManager.add('netbookingsShortcodesExtensions', tinymce.plugins.netbookingsShortcodesExtensions);
}());