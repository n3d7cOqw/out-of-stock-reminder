$(document).ready(function () {

    const grid = new window.prestashop.component.Grid("rules");
    // grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());

})