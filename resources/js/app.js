import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Quill from 'quill';

const ListFormat = Quill.import('formats/list');
class BulletList extends ListFormat {
  static blotName = 'bullet';
  static tagName = 'ul';
}
class OrderedList extends ListFormat {
  static blotName = 'ordered';
  static tagName = 'ol';
}
Quill.register(BulletList);
Quill.register(OrderedList);

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

    if (hiddenInput.value) {
      quill.root.innerHTML = hiddenInput.value;
    }

    quill.on('text-change', () => {
      hiddenInput.value = quill.root.innerHTML;
    });
  });
});
