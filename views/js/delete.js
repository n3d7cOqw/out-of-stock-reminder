
$(document).ready(function () {
    // Learn more about components in documentation
    // https://devdocs.prestashop.com/1.7/development/components/global-components/

    const grid = new window.prestashop.component.Grid("rules");
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());


});