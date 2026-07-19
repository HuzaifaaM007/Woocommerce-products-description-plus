document.addEventListener('DOMContentLoaded', function () {

    const customPromptEnabled = document.getElementById('wcpdp_enable_custom_prompt');
    const prompt = document.getElementById('wcpdp_custom_ai_prompt');

    function togglePrompt() {
        prompt.disabled = !customPromptEnabled.checked;
    }

    togglePrompt();

    customPromptEnabled.addEventListener('change', togglePrompt);

});