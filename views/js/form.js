
$(document).ready(function () {
    window.prestashop.component.initComponents([
        'ChoiceTree',
        'ChoiceTable',
    ]);
});
// $(document).ready(function() {
//     window.prestashop.component.initComponents([
//         'ChoiceTree',
//         'ChoiceTable',
//     ]);
// });


$(document).ready(function () {
    // Learn more about components in documentation
    // https://devdocs.prestashop.com/1.7/development/components/global-components/
    new window.prestashop.component.ChoiceTree('.choice-tree-actions');
});

//ChoiceTable