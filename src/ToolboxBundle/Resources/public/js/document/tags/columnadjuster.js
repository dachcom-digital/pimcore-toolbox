pimcore.registerNS('pimcore.document.tags.columnadjuster');
pimcore.document.tags.columnadjuster = Class.create(pimcore.document.tag, {

    getType: function () {
        return 'columnadjuster';
    },

    /**
     * @param id
     * @param name
     * @param options
     * @param data
     * @param inherited
     */
    initialize: function (id, name, options, data, inherited) {

        this.id = id;
        this.name = name;
        this.data = data;

        this.buttonHolder = null;
        this.currentColumnSelection = null;
        this.currentColumnSelectionName = null;

        this.adjustmentFailed = false;
        this.gridEditorActive = false;
        this.gridForm = null;
        this.toolbar = null;
        this.gridPreview = {};
        this.options = this.parseOptions(options);
        this.editWindowState = {w: 0, h: 0};

        this.combos = {};
        this.inheritWatcher = {};
        this.breakPoints = [];

        this.gridAmount = 0;
        this.gridSize = 0;

        this.setupWrapper();

        var columnSelector = Ext.get(this.id).up('.t-row').prev('.t-row').query('.pimcore_tag_select');
        if (columnSelector.length === 0 || !columnSelector[0].firstChild) {
            return;
        }

        this.gridSelector = Ext.getCmp(columnSelector[0].firstChild.id);
        if (!this.gridSelector) {
            return;
        }

        //set current column selection
        this.currentColumnSelection = this.gridSelector.getValue();
        this.currentColumnSelectionName = this.gridSelector.getRawValue();
        this.buttonHolder = Ext.get(id);

        this.statusButton = new Ext.form.Checkbox({
            fieldLabel: t('enable_column_adjuster'),
            checked: this.data !== false,
            labelWidth: 170,
            labelStyle: 'padding-top:0; font-weight: 300;',
            listeners: {
                'change': function (b) {
                    //reset data if adjuster is disabled!
                    if (b.checked === false) {
                        this.data = false;
                    }
                    this.gridEditButton.setHidden(!b.checked)
                }.bind(this)
            }
        });

        this.gridEditButton = new Ext.Button({
            iconCls: 'toolbox_column_adjuster',
            text: t('edit_column_configuration'),
            hidden: this.data === false,
            style: 'background-color: white;',
            listeners: {
                'click': function () {
                    if (this.gridEditorActive === true) {
                        this.closeEditor()
                    } else {
                        this.expandEditor()
                    }
                }.bind(this)
            }
        });

        var checkInitState = function () {
            //there is only 1 column: we don't need a column adjuster...
            if (this.currentColumnSelection.split('_').length === 2) {
                this.data = false;
                this.gridEditButton.setDisabled(true);
                this.statusButton.setValue(false);
                this.statusButton.setDisabled(true);
            } else {
                this.gridEditButton.setDisabled(false);
                this.statusButton.setDisabled(false);
            }
        }.bind(this);

        this.gridSelector.addListener('change', function () {
            this.statusButton.setValue(false);
            this.currentColumnSelection = this.gridSelector.getValue();
            this.currentColumnSelectionName = this.gridSelector.getRawValue();
            checkInitState();
        }.bind(this));

        checkInitState();

        this.toolbar = new Ext.form.FormPanel({
            layout: 'fit',
            tbar: [this.statusButton, '->', this.gridEditButton]
        });

        this.gridForm = new Ext.FormPanel({
            itemId: 'form',
            scrollable: true,
            hidden: true,
            layout: 'fit',
            style: 'margin: 10px 0;'
        });

        this.toolbar.render(this.buttonHolder);
        this.gridForm.render(this.buttonHolder);

    },

    expandEditor: function () {

        if (this.gridEditorActive === true) {
            return;
        }

        var windowSelector = Ext.get(this.id).up('.x-window');
        if (windowSelector.length === 0) {
            return;
        }

        var editWindow = Ext.getCmp(windowSelector.id);
        if (!editWindow) {
            return;
        }

        this.gridEditorActive = true;
        this.gridEditButton.setText(t('close_column_configuration'));
        this.statusButton.setDisabled(true);
        this.gridSelector.setDisabled(true);

        this.editWindowState.w = editWindow.getWidth();
        this.editWindowState.h = editWindow.getHeight();

        var cancelButton = Ext.ComponentQuery.query('button[iconCls=pimcore_icon_cancel]', editWindow);
        if (cancelButton.length === 1) {
            cancelButton[0].setDisabled(true);
        }

        editWindow.addCls('grid-adjuster-active');
        editWindow.setWidth(900);
        editWindow.setHeight(600).center();

        this.populateGridForm();
        this.toolbar.updateLayout();
        this.gridForm.setHidden(false);

    },

    closeEditor: function () {

        if (this.gridEditorActive === false) {
            return;
        }

        var windowSelector = Ext.get(this.id).up('.x-window');
        if (windowSelector.length === 0) {
            return;
        }

        var editWindow = Ext.getCmp(windowSelector.id);
        if (!editWindow) {
            return;
        }

        this.gridEditorActive = false;
        this.gridEditButton.setText(t('edit_column_configuration'));
        this.statusButton.setDisabled(false);
        this.gridSelector.setDisabled(false);

        var cancelButton = Ext.ComponentQuery.query('button[iconCls=pimcore_icon_cancel]', editWindow);
        if (cancelButton.length === 1) {
            cancelButton[0].setDisabled(false);
        }

        editWindow.removeCls('grid-adjuster-active');
        editWindow.setWidth(this.editWindowState.w);
        editWindow.setHeight(this.editWindowState.h).center();

        this.gridForm.removeAll(true);
        this.gridForm.updateLayout();
        this.toolbar.updateLayout();
        this.gridForm.setHidden(true);

    },

    /**
     * @returns {null|Ext.FormPanel}
     */
    populateGridForm: function () {
        var _ = this,
            currentDocumentId = null,
            tabPanel = new Ext.TabPanel({
                title: t('grid_configuration_for') + ' "' + this.currentColumnSelectionName + '"',
                closable: false,
                layout: 'fit',
                activeTab: 0,
                border: false
            });

        this.gridForm.add(tabPanel);

        if (typeof pimcore_document_id !== 'undefined') {
            currentDocumentId = pimcore_document_id;
        }

        Ext.Ajax.request({
            url: '/admin/toolbox-get-column-info',
            params: {
                currentColumn: this.currentColumnSelection,
                customColumnConfiguration: Ext.encode(this.data),
                tb_document_request_id: currentDocumentId
            },
            success: function (response) {

                var res = Ext.decode(response.responseText),
                    validResponse = true;

                //invalid column configuration
                if (res.breakPoints === false) {
                    _.cancelAdjustment('no breakpoints for current selection found.');
                    return;
                }

                //Set global breakpoints!
                _.breakPoints = res.breakPoints;
                _.gridSize = res.gridSize;
                _.columnStore = res.columnStore;
                _.layout = res.layout;

                //Map breakpoint data with current one!
                _.mergeCustomGridValue();

                Ext.Array.each(_.breakPoints, function (breakpoint, breakpointIndex) {

                    //invalid grid configuration
                    if (!breakpoint.grid || breakpoint.grid.length === 0) {
                        validResponse = false;
                        _.cancelAdjustment('no valid grid amount for breakpoint found.');
                        return false;
                    }

                    if (breakpointIndex === 0) {
                        _.gridAmount = breakpoint.grid.length;
                    }

                    if (_.gridAmount !== breakpoint.grid.length) {
                        validResponse = false;
                        _.cancelAdjustment('grid amount mismatch.');
                        return false;
                    }

                    var title = breakpoint.name ? breakpoint.name : 'Breakpoint: ' + breakpoint.identifier,
                        tab = new Ext.Panel({
                            title: title,
                            autoScroll: true,
                            forceLayout: true,
                            border: false
                        });

                    var compositeField = new Ext.form.FieldContainer({
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding:5px 10px;'
                    });

                    var gridLayoutForPreview = [],
                        isInherited = breakpoint.grid.filter(function (grid) {
                            return grid.value === null
                        }).length > 0;

                    breakpoint.inherit = isInherited;
                    _.combos[breakpoint.identifier] = [];

                    Ext.Array.each(breakpoint.grid, function (grid, gridIndex) {
                        var storeData = [],
                            hasOffset = _.gridColumnHasOffset(gridIndex),
                            inherited = grid.value === null,
                            realValue = grid.value === null ? _.findInheritedGridValue(breakpointIndex, gridIndex, 'value') : grid.value;

                        if(_.columnStore) {
                            Ext.Object.each(_.columnStore, function(k, v) {
                                storeData.push([k, v]);
                            });
                        } else {
                            for (var i = 1; i <= grid.amount; i++) {
                                storeData.push([i, ((100 / grid.amount) * i).toFixed(2) + '% (' + i + ')']);
                            }
                        }

                        var store = new Ext.data.ArrayStore({
                            fields: ['index', 'name'],
                            data: storeData
                        });

                        var columnIndex = gridIndex + 1;
                        //if offset available in index, add element!
                        if (hasOffset) {

                            var offsetStoreData = [],
                                realOffset = grid.offset === null ? _.findInheritedGridValue(breakpointIndex, gridIndex, 'offset') : grid.offset;

                            for (var oi = 0; oi < grid.amount; oi++) {
                                offsetStoreData.push([oi, ((100 / grid.amount) * oi).toFixed(2) + '% (' + oi + ')'])
                            }

                            var offsetCombo = new Ext.form.ComboBox({
                                flex: 1,
                                width: '120px',
                                padding: '1px',
                                name: 'breakpoint_' + breakpoint.identifier + '_o' + columnIndex,
                                fieldLabel: t('toolbox_column_offset'),
                                typeAhead: true,
                                mode: 'local',
                                forceSelection: true,
                                disabled: isInherited === true,
                                triggerAction: 'all',
                                labelAlign: 'top',
                                style: 'margin-right:2px;',
                                value: realOffset,
                                displayField: 'name',
                                valueField: 'index',
                                store: offsetStoreData,
                                cls: 'type-offset',
                                listeners: {
                                    change: _.updateData.bind(_, breakpoint.identifier)
                                }
                            });

                            gridLayoutForPreview.push({
                                'value': realOffset,
                                'inherit': inherited,
                                'offset': true
                            });

                            _.combos[breakpoint.identifier].push(offsetCombo);
                            compositeField.add(offsetCombo);

                        }

                        var combo = new Ext.form.ComboBox({
                            flex: 1,
                            width: '135px',
                            name: 'breakpoint_' + breakpoint.identifier + '_' + columnIndex,
                            fieldLabel: t('grid_adjuster_column') + ' ' + columnIndex,
                            typeAhead: true,
                            mode: 'local',
                            forceSelection: true,
                            disabled: isInherited === true,
                            triggerAction: 'all',
                            labelAlign: 'top',
                            style: 'margin-right:8px;',
                            value: realValue,
                            displayField: 'name',
                            valueField: 'index',
                            store: store,
                            cls: 'type-grid',
                            listeners: {
                                change: _.updateData.bind(_, breakpoint.identifier)
                            }
                        });

                        gridLayoutForPreview.push({'value': realValue, 'inherit': inherited, 'offset': false});

                        _.combos[breakpoint.identifier].push(combo);
                        compositeField.add(combo);

                    });

                    if (breakpointIndex !== 0) {
                        var inheritWatcher = new Ext.form.Checkbox({
                            boxLabel: t('toolbox_inherit_data'),
                            checked: isInherited,
                            flex: 0,
                            style: 'padding:0 0 0 10px; margin:0;',
                            submitValue: false,
                            listeners: {
                                'change': function (b) {
                                    b.setDisabled(true);
                                    setTimeout(function () {
                                        b.setDisabled(false);
                                    }, _.breakPoints.length * 20);
                                    Ext.Array.each(_.combos[breakpoint.identifier], function (c) {
                                        c.setDisabled(b.checked);
                                    });
                                    _.updateData(breakpoint.identifier);
                                }.bind(this)
                            }
                        });

                        _.inheritWatcher[breakpoint.identifier] = inheritWatcher;
                        tab.add(inheritWatcher);

                    }

                    tab.add(compositeField);

                    if (breakpoint.description) {
                        var descriptionField = new Ext.form.Label({
                            style: 'display:block; padding:5px; background:#f5f5f5; border:1px solid #eee; margin: 0 10px 10px 10px; font-weight: 300;',
                            text: breakpoint.description
                        });
                        tab.add(descriptionField);
                    }

                    var generatePreview = function (identifier, gridData) {
                        var gridHtml = '';
                        Ext.Array.each(gridData, function (grid, gridIndex) {
                            var val = grid.value;
                            gridHtml += new Ext.XTemplate("<div class='grid-pre-element pre-col-{colClass} {inherit} {offset}'></div>").applyTemplate({
                                colClass: val,
                                inherit: grid.inherit ? 'inherited' : '',
                                offset: grid.offset ? 'is-offset' : ''
                            })
                        });

                        return new Ext.XTemplate("<div class='{layout} grid-preview grid-size-{gridSize}'><div class='grid-pre-row'>{value}</div></div>").apply(
                            {
                                value: gridHtml,
                                gridSize: _.gridSize,
                                layout: _.layout
                            }
                        );
                    };

                    _.gridPreview[breakpoint.identifier] = new Ext.form.Panel({
                        html: generatePreview(breakpoint.identifier, gridLayoutForPreview),
                        style: 'padding: 0 10px 10px 10px; font-weight: 300;',
                        border: false,
                        layout: 'fit',
                        listeners: {
                            'updateGridLayout': function (identifier, grid) {
                                this.update(generatePreview(identifier, grid));
                            }
                        }
                    });

                    tab.add(_.gridPreview[breakpoint.identifier]);
                    tabPanel.add(tab);
                });

                if (validResponse === true) {
                    tabPanel.setActiveTab(0);
                    _.toolbar.updateLayout();
                    _.gridForm.updateLayout();
                }


            }.bind(this)
        });

        return this.gridForm;

    },

    /**
     * @param identifier
     * @param triggerEl
     */
    updateData: function (identifier, triggerEl) {
        var form = this.gridForm.getForm(),
            _ = this;

        if (!form.isValid() || this.adjustmentFailed === true) {
            return;
        }

        var values = form.getFieldValues();

        // update values
        Ext.Object.each(values, function (val, columnAmount) {
            var elements = val.split('_'),
                breakpointIdentifier = elements[1],
                columnIndex = elements[2],
                breakPointIndex = _.findBreakPointDataByIdentifier(breakpointIdentifier);

            if (!isNaN(columnIndex) && typeof _.breakPoints[breakPointIndex]['grid'] !== 'undefined') {
                if (typeof _.breakPoints[breakPointIndex]['grid'][columnIndex] === 'undefined') {
                    _.breakPoints[breakPointIndex]['grid'][columnIndex - 1] = {};
                }
                _.breakPoints[breakPointIndex]['grid'][columnIndex - 1].value = columnAmount;

                //check offset!
                _.breakPoints[breakPointIndex]['grid'][columnIndex - 1].offset = null;
                if (typeof values['breakpoint_' + breakpointIdentifier + '_o' + columnIndex] !== 'undefined') {
                    _.breakPoints[breakPointIndex]['grid'][columnIndex - 1].offset = values['breakpoint_' + breakpointIdentifier + '_o' + columnIndex];
                }
            }
        });

        // check inherit data
        Ext.Array.each(_.breakPoints, function (breakpoint, breakPointIndex) {
            breakpoint.inherit = false;
            if (typeof _.inheritWatcher[breakpoint.identifier] !== 'undefined') {
                breakpoint.inherit = _.inheritWatcher[breakpoint.identifier].checked;
            }
        });

        //start with highest breakpoint - check all inheritances
        function cycleBreakpoints(breakpointIndex) {

            if (_.adjustmentFailed === true || breakpointIndex < 0) {
                return;
            }

            setTimeout(function () {
                var gridLayoutForPreview = [],
                    breakpoint = _.breakPoints[breakpointIndex];
                if (breakpoint.inherit === true) {
                    var gridIndex = 0;
                    Ext.Array.each(_.combos[breakpoint.identifier], function (combo) {
                        var property = combo.cls.indexOf('type-grid') !== -1 ? 'value' : 'offset',
                            realValue = _.findInheritedGridValue(breakpointIndex, gridIndex, property);
                        combo.suspendEvent('change');
                        combo.setValue(realValue);
                        combo.resumeEvent('change');
                        _.breakPoints[breakpointIndex]['grid'][gridIndex][property] = realValue;
                        gridLayoutForPreview.push({
                            'value': realValue,
                            'inherit': breakpoint.inherit,
                            'offset': combo.cls.indexOf('type-offset') !== -1
                        });
                        if (property === 'value') {
                            gridIndex++;
                        }
                    });
                } else {
                    Ext.Array.each(_.combos[breakpoint.identifier], function (combo) {
                        gridLayoutForPreview.push({
                            'value': combo.getValue(),
                            'inherit': false,
                            'offset': combo.cls.indexOf('type-offset') !== -1
                        });
                    });
                }
                _.gridPreview[breakpoint.identifier].fireEvent('updateGridLayout', breakpoint.identifier, gridLayoutForPreview);
                cycleBreakpoints(--breakpointIndex);
            }, 20);
        };

        cycleBreakpoints(_.breakPoints.length - 1);

        var breakpointData = {'breakpoints': {}},
            validData = true;
        Ext.Array.each(_.breakPoints, function (breakpoint, breakPointIndex) {
            if (breakpoint.inherit === false) {
                var elements = breakpoint.grid.map(function (a) {
                    var offset = a.offset !== undefined && a.offset !== null ? 'o' + a.offset + '_' : '';
                    return offset + a.value;
                });

                if (elements.length !== _.gridAmount) {
                    validData = false;
                    return false;
                }

                breakpointData['breakpoints'][breakpoint.identifier] = elements.join('_');
            }
        });

        if (validData === false) {
            _.adjustmentFailed = true;
            _.cancelAdjustment('there was a error while recalculating the custom grid configuration.', 250);
            return;
        }

        ///console.log(_.breakPoints, breakpointData);

        this.data = breakpointData;

    },

    /**
     * @returns {string}
     */
    mergeCustomGridValue: function () {

        if (!this.data.breakpoints) {
            return null;
        }

        Ext.Array.each(this.breakPoints, function (breakpoint, breakpointIndex) {
            if (typeof this.data.breakpoints[breakpoint.identifier] !== 'undefined') {
                var storedGridData = this.data.breakpoints[breakpoint.identifier].split('_'),
                    gridValues = storedGridData.filter(function (val) {
                        return !isNaN(val)
                    }),
                    offsetValues = storedGridData.filter(function (val) {
                        return val.charAt(0) === 'o'
                    });

                Ext.Array.each(breakpoint.grid, function (grid, gridIndex) {
                    if (typeof gridValues[gridIndex] !== 'undefined') {
                        breakpoint.grid[gridIndex].value = gridValues[gridIndex];
                        if (typeof offsetValues[gridIndex] !== 'undefined') {
                            value = offsetValues[gridIndex].substr(1);
                            breakpoint.grid[gridIndex].offset = value;
                        }
                    }
                });
            }
        }.bind(this));
    },

    /**
     * @param currentBreakpointIndex
     * @param gridIndex
     * @param returnProperty
     * @returns {*}
     */
    findInheritedGridValue: function (currentBreakpointIndex, gridIndex, returnProperty) {

        var val = null,
            currentIndex = currentBreakpointIndex - 1;

        while (val === null && currentIndex >= 0) {
            if (typeof this.breakPoints[currentIndex] !== 'undefined' && this.breakPoints[currentIndex].inherit === false) {
                if (this.breakPoints[currentIndex]['grid'][gridIndex][returnProperty] !== null) {
                    val = this.breakPoints[currentIndex]['grid'][gridIndex][returnProperty];
                    break;
                }
            }
            currentIndex--;
        }

        return val;

    },

    /**
     * @param identifier
     * @returns {*}
     */
    findBreakPointDataByIdentifier: function (identifier) {
        var currentBreakpointIndex = null;
        Ext.Array.each(this.breakPoints, function (breakpoint, breakpointIndex) {
            if (breakpoint.identifier === identifier) {
                currentBreakpointIndex = breakpointIndex;
                return false;
            }
        });

        return currentBreakpointIndex;
    },

    /**
     *
     * @param gridIndex
     * @returns {boolean}
     */
    gridColumnHasOffset: function (gridIndex) {
        var hasOffset = false;
        Ext.Array.each(this.breakPoints, function (breakpoint, breakpointIndex) {
            hasOffset = breakpoint.grid.filter(function (grid, i) {
                return i === gridIndex && grid.offset !== null
            }).length > 0;
            if (hasOffset === true) {
                return false;
            }
        });

        return hasOffset;
    },

    /**
     *
     * @param message
     * @param startDelayed
     */
    cancelAdjustment: function (message, startDelayed) {
        this.statusButton.setValue(false);
        this.adjustmentFailed = false;
        this.data = false;
        setTimeout(function () {
            this.closeEditor();
            Ext.MessageBox.alert(t('error'), t('invalid_column_configuration') + ' "' + message + '"');
        }.bind(this), startDelayed ? startDelayed : 0);
    },

    /**
     * @returns {*|boolean}
     */
    getValue: function () {
        return this.data;
    }
});