const colorPicker = {
    props: {
        colorValue: {
            type: String,
            default: ''
        }
    },
    template: `
        <q-btn size="sm" :style="css">
            <q-popup-proxy class="z-max" cover transition-show="scale" transition-hide="scale">
                <q-color v-model="colorValue" format-model="hex" default-view="palette" @update:model-value="processColorChange"></q-color>
            </q-popup-proxy>
        </q-btn>
    `,
    setup(props, context) {
        const css = Vue.ref('');
        const propsRefs = Vue.toRefs(props);

        Vue.watch(propsRefs.colorValue, () => {
            setCSS();
        });

        function processColorChange(val) {
            context.emit('update:color-picker', val);
        }

        function setCSS() {
            css.value = 'background-color: ' + props.colorValue + ';border: 1px solid black;';
        }

        Vue.onMounted(() => {
            setCSS();
        });

        return {
            css,
            processColorChange
        }
    }
};
