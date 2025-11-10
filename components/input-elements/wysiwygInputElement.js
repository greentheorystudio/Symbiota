const wysiwygInputElement = {
    props: {
        clearable: {
            type: Boolean,
            default: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: ''
        },
        tabindex: {
            type: Number,
            default: 0
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-editor v-model="value" :toolbar="editorToolbarConfig" :fonts="editorToolbarFonts" :placeholder="label" :disable="disabled" :tabindex="tabindex" @update:model-value="processValueChange"></q-editor>
    `,
    setup(props, context) {
        const $q = useQuasar();

        const editorToolbarConfig = Vue.ref([
            [
                {
                    label: $q.lang.editor.align,
                    icon: $q.iconSet.editor.align,
                    fixedLabel: true,
                    options: ['left', 'center', 'right', 'justify']
                }
            ],
            ['bold', 'italic', 'strike', 'underline', 'subscript', 'superscript'],
            ['hr', 'link'],
            [
                {
                    label: $q.lang.editor.formatting,
                    icon: $q.iconSet.editor.formatting,
                    list: 'no-icons',
                    options: [
                        'p',
                        'h1',
                        'h2',
                        'h3',
                        'h4',
                        'h5',
                        'h6',
                        'code'
                    ]
                },
                {
                    label: $q.lang.editor.fontSize,
                    icon: $q.iconSet.editor.fontSize,
                    fixedLabel: true,
                    fixedIcon: true,
                    list: 'no-icons',
                    options: [
                        'size-1',
                        'size-2',
                        'size-3',
                        'size-4',
                        'size-5',
                        'size-6',
                        'size-7'
                    ]
                },
                {
                    label: $q.lang.editor.defaultFont,
                    icon: $q.iconSet.editor.font,
                    fixedIcon: true,
                    list: 'no-icons',
                    options: [
                        'default_font',
                        'arial',
                        'arial_black',
                        'comic_sans',
                        'courier_new',
                        'impact',
                        'lucida_grande',
                        'times_new_roman',
                        'verdana'
                    ]
                },
                'removeFormat'
            ],
            ['quote', 'unordered', 'ordered', 'outdent', 'indent'],
            ['undo', 'redo'],
            ['viewsource']
        ]);
        const editorToolbarFonts = Vue.ref({
            arial: 'Arial',
            arial_black: 'Arial Black',
            comic_sans: 'Comic Sans MS',
            courier_new: 'Courier New',
            impact: 'Impact',
            lucida_grande: 'Lucida Grande',
            times_new_roman: 'Times New Roman',
            verdana: 'Verdana'
        });

        function processValueChange(val) {
            context.emit('update:value', val);
        }

        return {
            editorToolbarConfig,
            editorToolbarFonts,
            processValueChange
        }
    }
};
