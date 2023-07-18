const humanValidator = {
    template: `
        <div class="column justify-center">
            <div class="row justify-center">
                <canvas ref="humanValidationCanvasRef" class="human-validator-canvas"></canvas>
            </div>
            <div class="row justify-center q-mt-sm">
                <q-input ref="humanValidationRef" outlined bottom-slots v-model="humanValidationValue" label="Enter the numbers in the box above" bg-color="white" class="col-4" dense lazy-rules :rules="humanValidationRules">
                    <template v-slot:hint>
                        Required
                    </template>
                </q-input>
            </div>
        </div>
    `,
    setup () {
        const humanValidationValue = Vue.ref(null);
        const humanValidationRef = Vue.ref(null);
        const humanValidationCanvasRef = Vue.ref(null);
        let randNumber = Vue.ref(0);
        const verifyHuman = (val) => {
            if(val.toString() === randNumber.toString()){
                return new Promise((resolve) => {
                    setTimeout(() => {
                        resolve(humanValidationValue.value.toString() === randNumber.toString() || 'Numbers must match what is displayed in the box above');
                    }, 500 );
                });
            }
            else{
                return 'Numbers must match what is displayed in the box above';
            }
        };
        return {
            humanValidationValue,
            humanValidationRef,
            humanValidationCanvasRef,
            humanValidationRules: [
                val => (val !== null && val !== '') || 'Required',
                val => verifyHuman(val)
            ],
            setRandomNumber() {
                const randNumb1 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb2 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb3 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb4 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb5 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb6 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb7 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb8 = (Math.round(Math.random() * 9.5)).toString();
                const randNumb9 = (Math.round(Math.random() * 9.5)).toString();
                randNumber = randNumb1 + randNumb2 + randNumb3 + randNumb4 + randNumb5 + randNumb6 + randNumb7 + randNumb8 + randNumb9;
                const ctx = humanValidationCanvasRef.value.getContext("2d");
                ctx.clearRect(0, 0, humanValidationCanvasRef.value.width, humanValidationCanvasRef.value.height);
                ctx.font = "55px Times New Roman";
                ctx.textAlign = "center";
                ctx.fillText(randNumber.toString(), humanValidationCanvasRef.value.width/2, humanValidationCanvasRef.value.height/2);
                if(humanValidationValue.value && humanValidationValue.value.toString().length > 0){
                    humanValidationRef.value.validate();
                }
                setTimeout(this.setRandomNumber, 120000);
            }
        }
    },
    mounted() {
        this.setRandomNumber();
    },
    methods: {
        formHasErrors() {
            return this.humanValidationRef.hasError;
        },
        validateForm() {
            this.humanValidationRef.validate();
        }
    }
};
