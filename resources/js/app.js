import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Quill from 'quill';

window.Alpine = Alpine;
window.Quill = Quill;
Alpine.plugin(collapse);
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
  const editors = document.querySelectorAll('[data-quill-editor]');
  editors.forEach(el => {
    const hiddenInput = document.getElementById(el.getAttribute('data-quill-editor'));
    const quill = new Quill(el, {
      theme: 'snow',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          ['blockquote', 'code-block'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link', 'image'],
          ['clean']
        ]
      },
      placeholder: 'Enter content...'
    });

    if (hiddenInput.value) {
      quill.root.innerHTML = hiddenInput.value;
    }

    quill.on('text-change', () => {
      hiddenInput.value = quill.root.innerHTML;
    });
  });
});
