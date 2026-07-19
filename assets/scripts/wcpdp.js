document.addEventListener('DOMContentLoaded', function () {
    const gen_desc_btn = document.getElementById('wcpdp_ai_btn');

    if (!gen_desc_btn) {
        return;
    }
    
    gen_desc_btn.addEventListener('click', async (e) => {
        e.preventDefault();

        var product_id = document.getElementById('post_ID').value;
        var original_status = document.getElementById('original_post_status')?.value;
        var is_virtual = document.getElementById('_virtual')?.checked ? 1 : 0;


        gen_desc_btn.disabled = true;
        gen_desc_btn.textContent = 'Generating...';

        wcpdpShowPopup('Generating...', 'info');

        const formData = new FormData();
        formData.append('action', 'wcpdp_generate_description_ajax_request');
        formData.append('nonce', WCPDP_ai_nonce.nonce);
        formData.append('is_virtual', is_virtual);

        if (product_id && original_status !== 'auto-draft') {
            formData.append('product_id', product_id);
        }
        else {
            formData.append('name', document.getElementById('title')?.value || '');
            formData.append('regular_price', document.getElementById('_regular_price')?.value || '');
            formData.append('sale_price', document.getElementById('_sale_price')?.value || '');
            formData.append('sku', document.getElementById('_sku')?.value || '');


            formData.append('weight', document.getElementById('_weight')?.value || '');
            formData.append('length', document.getElementById('product_length')?.value || '');
            formData.append('width', document.getElementById('product_width')?.value || '');
            formData.append('height', document.getElementById('product_height')?.value || '');



            formData.append('product_type', document.getElementById('product-type')?.value || '');

            const categories = [...document.querySelectorAll('input[name="tax_input[product_cat][]"]:checked')]
                .map(el => el.closest('label') ? el.closest('label').textContent.trim() : '');

            categories.forEach(category => {
                formData.append('categories[]', category);
            });
        }

        try {
            const response = await fetch(WCPDP_ai_nonce.ajax_url, {
                method: 'POST',
                body: formData,
            });

            const result = await response.json()
                .then((data) => {
                    if (data.success) {
                        const { full_description, short_description } = data.data;

                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                            tinymce.get('content').setContent(full_description);
                        } else {
                            document.querySelector('#content').value = full_description;
                        }

                        if (typeof tinymce !== 'undefined' && tinymce.get('excerpt')) {
                            tinymce.get('excerpt').setContent(short_description);
                        } else {
                            document.querySelector('#excerpt_ifr').value = short_description;
                        }

                        wcpdpShowNotice('Descriptions generated successfully!', 'success');
                        wcpdpShowPopup('Description generated successfully!', 'success');
                    }
                    else {
                        wcpdpShowNotice(data.data, 'error');
                        wcpdpShowPopup(data.data, 'error');
                    }
                }).catch(
                    () => {
                        wcpdpShowNotice('Something went wrong. Please try again.', 'error');
                        wcpdpShowPopup('Something went wrong. Please try again.', 'error');
                    }
                ).finally(
                    () => {
                        console.log('finally reached');

                        gen_desc_btn.disabled = false;
                        gen_desc_btn.textContent = 'Generate Description';
                    }
                );

            console.log(result);

        } catch (error) {
            console.error(error);
        }
    })
})

function wcpdpShowNotice(message, type = 'success') {
    const existing = document.querySelector('.wcpdp-dynamic-notice');
    if (existing) existing.remove();

    const notice = document.createElement('div');
    notice.className = `notice notice-${type} is-dismissible wcpdp-dynamic-notice`;

    notice.innerHTML = `
        <p>${message}</p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    `;

    const heading = document.querySelector('.wrap h1, .wrap h2');

    if (heading) {
        heading.insertAdjacentElement('afterend', notice);
    } else {
        const wrapper = document.querySelector('#wpbody-content');
        wrapper.insertAdjacentElement('afterbegin', notice);
    }

    notice.scrollIntoView({ behavior: 'smooth', block: 'start' });

    notice.querySelector('.notice-dismiss').addEventListener('click', () => {
        notice.remove();
    });

    if (type === 'success') {
        setTimeout(() => notice.remove(), 4000);
    }
}

// function wcpdpShowPopup(message, type = 'success') {
//     const existing = document.querySelector('.wcpdp-dynamic-popup');
//     if (existing) existing.remove();

//     const colors = {
//         success: { bg: '#f0f9f0', border: '#46b450', text: '#1e4620' },
//         error:   { bg: '#fbeaea', border: '#dc3232', text: '#5c1a1a' },
//         info:    { bg: '#eaf3fb', border: '#00a0d2', text: '#0a3a52' },
//         warning: { bg: '#fef8e7', border: '#ffb900', text: '#5c4b00' }
//     };
//     const c = colors[type] || colors.info;

//     const popup = document.createElement('div');
//     popup.className = 'wcpdp-dynamic-popup';

//     popup.style.cssText = `
//         position: fixed;
//         bottom: 20px;
//         right: 20px;
//         max-width: 360px;
//         background: ${c.bg};
//         border-left: 4px solid ${c.border};
//         color: ${c.text};
//         padding: 12px 40px 12px 16px;
//         border-radius: 0;
//         box-shadow: 0 4px 14px rgba(0,0,0,0.15);
//         z-index: 999999;
//         font-size: 14px;
//         line-height: 1.5;
//         opacity: 0;
//         transform: translateY(20px);
//         transition: opacity 0.25s ease, transform 0.25s ease;
//     `;

//     popup.innerHTML = `
//         <span>${message}</span>
//         <button type="button" aria-label="Dismiss" style="
//             position: absolute;
//             top: 8px;
//             right: 8px;
//             background: none;
//             border: none;
//             font-size: 16px;
//             line-height: 1;
//             cursor: pointer;
//             color: ${c.text};
//             opacity: 0.6;
//         ">&times;</button>
//     `;

//     document.body.appendChild(popup);

//     requestAnimationFrame(() => {
//         popup.style.opacity = '1';
//         popup.style.transform = 'translateY(0)';
//     });

//     const removePopup = () => {
//         popup.style.opacity = '0';
//         popup.style.transform = 'translateY(20px)';
//         setTimeout(() => popup.remove(), 250);
//     };

//     popup.querySelector('button').addEventListener('click', removePopup);

//     if (type === 'success') {
//         setTimeout(removePopup, 4000);
//     }
// }

function wcpdpShowPopup(message, type = 'success') {
    const existing = document.querySelector('.wcpdp-dynamic-popup-overlay');
    if (existing) existing.remove();

    const colors = {
        success: { bg: '#f0f9f0', border: '#46b450', text: '#1e4620' },
        error: { bg: '#fbeaea', border: '#dc3232', text: '#5c1a1a' },
        info: { bg: '#eaf3fb', border: '#00a0d2', text: '#0a3a52' },
        warning: { bg: '#fef8e7', border: '#ffb900', text: '#5c4b00' }
    };
    const c = colors[type] || colors.info;

    const overlay = document.createElement('div');
    overlay.className = 'wcpdp-dynamic-popup-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        transition: background 0.25s ease;
    `;

    const popup = document.createElement('div');
    popup.className = 'wcpdp-dynamic-popup';
    popup.style.cssText = `
        position: relative;
        max-width: 380px;
        width: 90%;
        background: white;
        border-top: 4px solid ${c.border};
        color: ${c.text};
        padding: 28px 28px 28px 28px;
        border-radius: 6px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        font-size: 15px;
        line-height: 1.6;
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.25s ease, transform 0.25s ease;
    `;

    popup.innerHTML = `
        <span>${message}</span>
        <button type="button" aria-label="Dismiss" style="
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            color: ${c.text};
            opacity: 0.6;
        ">&times;</button>
    `;

    overlay.appendChild(popup);
    document.body.appendChild(overlay);

    requestAnimationFrame(() => {
        overlay.style.background = 'rgba(0,0,0,0.4)';
        popup.style.opacity = '1';
        popup.style.transform = 'scale(1)';
    });

    const removePopup = () => {
        overlay.style.background = 'rgba(0,0,0,0)';
        popup.style.opacity = '0';
        popup.style.transform = 'scale(0.9)';
        setTimeout(() => overlay.remove(), 250);
    };

    popup.querySelector('button').addEventListener('click', removePopup);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) removePopup();
    });

    const escHandler = (e) => {
        if (e.key === 'Escape') {
            removePopup();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);

    if (type === 'success') {
        setTimeout(removePopup, 4000);
    }
}