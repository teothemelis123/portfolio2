import FormHandler from './form-handler';

export default class Contact {
  static init() {
    const contactFormHandler = new FormHandler('.contact-form', 'contact', ['fullName', 'email', 'subject', 'message', 'store', 'phone']);

    contactFormHandler.registerSubmit((form, response) => {
      if (response === 200) {
        contactFormHandler.displayMessage(form, 'Your message has been sent!', 'success');
      } else {
        contactFormHandler.displayMessage(form, response, 'danger');
      }
    });
  }
}
