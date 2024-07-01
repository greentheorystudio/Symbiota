const occurrenceEditorResourcesTab = {
    template: `
        <div class="column q-gutter-sm">
            <occurrence-editor-checklist-voucher-module></occurrence-editor-checklist-voucher-module>
            <occurrence-editor-genetic-link-module></occurrence-editor-genetic-link-module>
        </div>
    `,
    components: {
        'occurrence-editor-checklist-voucher-module': occurrenceEditorChecklistVoucherModule,
        'occurrence-editor-genetic-link-module': occurrenceEditorGeneticLinkModule
    }
};
