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
          [{ 'list': 'bullet'}, { 'list': 'ordered' }],
          ['link', 'image'],
          ['clean']
        ]
      },
      placeholder: 'Enter content...'
    });

    // Load existing content and preserve list types
    if (hiddenInput.value) {
      const container = document.createElement('div');
      container.innerHTML = hiddenInput.value;
      quill.root.innerHTML = hiddenInput.value;
    }

    quill.on('text-change', () => {
      let html = quill.root.innerHTML;

      // Fix lists: ensure bullet lists use <ul> and ordered lists use <ol>
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;

      // Process all list items to fix list types based on their attributes
      tempDiv.querySelectorAll('li').forEach(li => {
        const parent = li.parentElement;
        const listType = li.getAttribute('data-list');

        if (listType === 'bullet' && parent.tagName === 'OL') {
          // Convert <ol> to <ul> for bullet lists
          const ul = document.createElement('ul');
          while (parent.firstChild) {
            ul.appendChild(parent.firstChild);
          }
          parent.replaceWith(ul);
        } else if (listType === 'ordered' && parent.tagName === 'UL') {
          // Convert <ul> to <ol> for ordered lists
          const ol = document.createElement('ol');
          while (parent.firstChild) {
            ol.appendChild(parent.firstChild);
          }
          parent.replaceWith(ol);
        }
      });

      hiddenInput.value = tempDiv.innerHTML;
    });
  });
});
