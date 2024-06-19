const useChecklistVoucherStore = Pinia.defineStore('checklist-voucher', {
    state: () => ({
        voucherArr: []
    }),
    getters: {
        getVoucherArr(state) {
            return state.voucherArr;
        }
    },
    actions: {
        clearVoucherArr() {
            this.voucherArr.length = 0;
        },
        createOccurrenceDeterminationRecord(collid, occid, callback) {
            this.determinationEditData['occid'] = occid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('determination', JSON.stringify(this.determinationEditData));
            formData.append('action', 'createOccurrenceDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        deleteDeterminationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('action', 'deleteDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        setDeterminationArr(occid) {
            const formData = new FormData();
            formData.append('occid', occid.toString());
            formData.append('action', 'getOccurrenceDeterminationArr');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.determinationArr = data;
            });
        }
    }
});
