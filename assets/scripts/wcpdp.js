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
                    }
                    else {
                        wcpdpShowNotice(data.data, 'error');
                    }
                }).catch(
                    () => {
                        wcpdpShowNotice('Something went wrong. Please try again.', 'error');
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