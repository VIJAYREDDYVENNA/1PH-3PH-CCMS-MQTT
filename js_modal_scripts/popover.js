const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

// Separate popover with dismiss on focus
const popoverDismissElement = document.querySelector('.popover-dismiss');
if (popoverDismissElement) {
    const popover = new bootstrap.Popover(popoverDismissElement, {
        trigger: 'focus'
    });
}
