const passwordInput = {
    props: {
        password: {
            type: String,
            default: null
        }
    },
    template: `
        <q-input ref="pwdRef" outlined bottom-slots v-model="password" type="password" label="Password" bg-color="white" class="col-4" dense lazy-rules :rules="passwordRules" @update:model-value="processChange">
            <template v-slot:hint>
                Required
            </template>
        </q-input>
        <q-input ref="pwd2Ref" outlined bottom-slots v-model="confirmationPassword" type="password" label="Confirm Password" bg-color="white" class="col-4" dense lazy-rules :rules="passwordRules">
            <template v-slot:hint>
                Required
            </template>
        </q-input>
    `,
    setup (props) {
        const confirmationPassword = Vue.ref(null);
        const pwdRef = Vue.ref(null);
        const pwd2Ref = Vue.ref(null);
        const validatePasswords = () => {
            if(props.password === confirmationPassword.value){
                pwdRef.value.resetValidation();
                pwd2Ref.value.resetValidation();
                return true;
            }
            else{
                return 'Password and confirmation password must match';
            }
        };
        return {
            confirmationPassword,
            pwdRef,
            pwd2Ref,
            passwordRules: [
                val => (val !== null && val !== '') || 'Required',
                val => (val && val.length > 6) || 'Password must be longer than six characters',
                () => validatePasswords()
            ]
        }
    },
    methods: {
        formHasErrors() {
            return (this.pwdRef.hasError || this.pwd2Ref.hasError);
        },
        processChange(value) {
            this.$emit('update:password', value);
        },
        validateForm() {
            this.pwdRef.validate();
            this.pwd2Ref.validate();
        }
    }
};
