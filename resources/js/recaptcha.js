const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;

function setRecaptchaToken(form, token) {
    let input = form.querySelector('input[name="g-recaptcha-response"]');

    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'g-recaptcha-response';
        form.appendChild(input);
    }

    input.value = token;
}

export function initRecaptchaForms() {
    if (!siteKey) {
        return;
    }

    document.querySelectorAll('form[data-recaptcha]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const action = form.dataset.recaptcha;

            if (!window.grecaptcha) {
                form.submit();

                return;
            }

            window.grecaptcha.ready(() => {
                window.grecaptcha
                    .execute(siteKey, { action })
                    .then((token) => {
                        setRecaptchaToken(form, token);
                        form.submit();
                    })
                    .catch(() => {
                        form.submit();
                    });
            });
        });
    });
}
