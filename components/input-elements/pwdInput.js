const passwordInput = {
    props: {
        password: {
            type: String,
            default: null
        },
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-input ref="pwdRef" outlined bottom-slots v-model="passwordValue" type="password" autocomplete="new-password" label="Password" bg-color="white" class="col-4" dense lazy-rules :rules="passwordRules" :tabindex="tabindex" @update:model-value="processChange">
            <template v-slot:hint>
                Required
            </template>
        </q-input>
        <q-input ref="pwd2Ref" outlined bottom-slots v-model="confirmationPasswordValue" type="password" autocomplete="new-password" label="Confirm Password" bg-color="white" class="col-4" :tabindex="tabindex" dense lazy-rules :rules="passwordRules">
            <template v-slot:hint>
                Required
            </template>
        </q-input>
    `,
    setup(props, context) {
        const confirmationPasswordValue = Vue.ref(null);
        const passwordValue = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const pwdRef = Vue.ref(null);
        const pwd2Ref = Vue.ref(null);
        const validatePasswords = () => {
            if(passwordValue.value === confirmationPasswordValue.value){
                pwdRef.value.resetValidation();
                pwd2Ref.value.resetValidation();
                return true;
            }
            else{
                return 'Password and confirmation password must match';
            }
        };

        Vue.watch(propsRefs.password, () => {
            setPasswordValue();
        });

        function formHasErrors() {
            return (pwdRef.value.hasError || pwd2Ref.value.hasError);
        }

        function processChange(value) {
            context.emit('update:password', value);
        }

        function setPasswordValue() {
            passwordValue.value = props.password;
        }

        function validateForm() {
            pwdRef.value.validate();
            pwd2Ref.value.validate();
        }

        Vue.onMounted(() => {
            setPasswordValue();
        });

        return {
            confirmationPasswordValue,
            passwordValue,
            pwdRef,
            pwd2Ref,
            passwordRules: [
                val => (val !== null && val !== '') || 'Required',
                val => (val && /[A-Z]/.test(val)) || 'Password must include an uppercase letter',
                val => (val && /[a-z]/.test(val)) || 'Password must include a lowercase letter',
                val => (val && /[0-9~!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g.test(val)) || 'Password must include a number or special character',
                val => (val && val.length >= 12) || 'Password must be at least 12 characters',
                () => validatePasswords()
            ],
            formHasErrors,
            processChange,
            validateForm
        }
    }
};
