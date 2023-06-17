const taxaProfileTaxonVernaculars = {
    props: [
        'vernaculars'
    ],
    watch: {
        vernaculars: function(){
            this.processVernaculars();
        }
    },
    template: `
        <template v-if="vernaculars.length">
            <div id="vernaculars">
                <template v-if="vernaculars.length > 1">
                    <template v-if="!showAll">
                        {{ firstVernacular }}<span @click="showAll = true" class="cursor-pointer" title="Click here to show more common names">,&nbsp;&nbsp;[more...]</span>
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
    data() {
        return {
            vernacularStr: Vue.ref(null),
            firstVernacular: Vue.ref(null),
            loaded: Vue.ref(false),
            showAll: Vue.ref(false)
        };
    },
    mounted(){
        this.processVernaculars();
    },
    methods: {
        processVernaculars() {
            if(this.vernaculars.length > 0){
                this.firstVernacular = this.vernaculars[0];
                this.vernacularStr = this.vernaculars.join(', ');
            }
        }
    }
};
