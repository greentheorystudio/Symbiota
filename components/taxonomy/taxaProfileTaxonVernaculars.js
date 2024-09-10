const taxaProfileTaxonVernaculars = {
    props: {
        vernaculars: {
            type: Array,
            default: []
        }
    },
    template: `
        <template v-if="vernaculars.length">
            <div id="vernaculars">
                <template v-if="vernaculars.length > 1">
                    <template v-if="!showAll">
                        {{ firstVernacular }}<span @click="showAll = true" class="cursor-pointer" title="Click here to show more common names">&nbsp;&nbsp;[more...]</span>
                    </template>
                    <template v-else>
                        {{ vernacularStr }}<span @click="showAll = false" class="cursor-pointer" title="Click here to show less common names">&nbsp;&nbsp;[less]</span>
                    </template>
                </template>
                <template v-else>
                    {{ firstVernacular }}
                </template>
            </div>
        </template>
    `,
    setup(props) {
        const firstVernacular = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const showAll = Vue.ref(false);
        const vernacularStr = Vue.ref(null);

        Vue.watch(propsRefs.vernaculars, () => {
            processVernaculars();
        });

        function processVernaculars() {
            if(props.vernaculars.length > 0){
                firstVernacular.value = props.vernaculars[0];
                vernacularStr.value = props.vernaculars.join(', ');
            }
        }

        Vue.onMounted(() => {
            processVernaculars();
        });

        return {
            firstVernacular,
            showAll,
            vernacularStr
        }
    }
};
