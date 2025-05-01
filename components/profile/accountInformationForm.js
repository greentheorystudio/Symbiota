const accountInformationForm = {
    template: `
        <div class="row justify-start q-gutter-md q-mt-xs">
            <q-input ref="firstnameRef" outlined bottom-slots v-model="user.firstname" label="First Name" bg-color="white" class="col-grow" dense lazy-rules :rules="requiredRules" @update:model-value="(value) => processChange('firstname', value)">
                <template v-slot:hint>
                    Required
                </template>
            </q-input>
            <q-input outlined v-model="user.middleinitial" label="Middle Initial" bg-color="white" class="col-2" dense @update:model-value="(value) => processChange('middleinitial', value)"></q-input>
            <q-input ref="lastnameRef" outlined bottom-slots v-model="user.lastname" label="Last Name" bg-color="white" class="col-grow" dense lazy-rules :rules="requiredRules" @update:model-value="(value) => processChange('lastname', value)">
                <template v-slot:hint>
                    Required
                </template>
            </q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-xs">
            <q-input ref="emailRef" outlined bottom-slots v-model="user.email" type="email" label="Email Address" bg-color="white" class="col-grow" dense lazy-rules :rules="emailRules" @update:model-value="(value) => processChange('email', value)">
                <template v-slot:hint>
                    Required
                </template>
            </q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-xs">
            <q-input outlined v-model="user.title" label="Title" bg-color="white" class="col-3" dense @update:model-value="(value) => processChange('title', value)"></q-input>
            <q-input outlined v-model="user.institution" label="Institution" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('institution', value)"></q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-md">
            <q-input outlined v-model="user.department" label="Department" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('department', value)"></q-input>
            <q-input outlined v-model="user.address" label="Street Address" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('address', value)"></q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-md">
            <q-input outlined v-model="user.city" label="City" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('city', value)"></q-input>
            <q-input outlined v-model="user.state" label="State" bg-color="white" class="col-4" dense @update:model-value="(value) => processChange('state', value)"></q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-md">
            <q-input outlined v-model="user.zip" label="Zip Code" bg-color="white" class="col-3" dense @update:model-value="(value) => processChange('zip', value)"></q-input>
            <q-input outlined v-model="user.country" label="Country" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('country', value)"></q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-md">
            <q-input outlined v-model="user.url" label="URL" bg-color="white" class="col-grow" dense @update:model-value="(value) => processChange('url', value)"></q-input>
        </div>
        <div class="row justify-start q-gutter-md q-mt-md">
            <q-input outlined v-model="user.biography" label="Biography" bg-color="white" class="col-grow" dense autogrow @update:model-value="(value) => processChange('biography', value)"></q-input>
        </div>
    `,
    setup(_, context) {
        const userStore = useUserStore();

        const emailExists = (val) => {
            return new Promise((resolve) => {
                const formData = new FormData();
                formData.append('email', val);
                formData.append('action', 'getUserFromEmail');
                fetch(profileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    const uId = resObj.hasOwnProperty('uid') ? Number(resObj['uid']) : 0;
                    resolve(((user.value.uid && Number(user.value.uid) === uId) || uId === 0) || 'Email address is already associated with another account');
                });
            });
        };
        const emailRef = Vue.ref(null);
        const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        const firstnameRef = Vue.ref(null);
        const lastnameRef = Vue.ref(null);
        const user = Vue.computed(() => userStore.getUserData);

        function formHasErrors() {
            return (
                firstnameRef.value.hasError ||
                lastnameRef.value.hasError ||
                emailRef.value.hasError
            );
        }

        function processChange(key, value) {
            context.emit('update:account-information', {key: key, value: ((value && value !== '') ? value : null)});
        }

        function validateForm() {
            firstnameRef.value.validate();
            lastnameRef.value.validate();
            emailRef.value.validate();
        }

        return {
            emailRef,
            emailRules: [
                val => (val !== null && val !== '') || 'Required',
                val => emailRegex.test(val) || 'Please enter a valid email address',
                val => emailExists(val)
            ],
            firstnameRef,
            lastnameRef,
            requiredRules: [
                val => (val !== null && val !== '') || 'Required'
            ],
            user,
            formHasErrors,
            processChange,
            validateForm
        }
    }
};
