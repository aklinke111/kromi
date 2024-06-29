// Laden der gespeicherten Notizen beim Seitenstart
window.onload = function() {
    loadNotes();
    launchFullscreen(); // App im Vollbildmodus starten
};

// Funktion zum Laden der gespeicherten Notizen aus localStorage
function loadNotes() {
    var savedNotes = JSON.parse(localStorage.getItem('notes')) || [];
    var noteList = document.getElementById('noteList');
    noteList.innerHTML = ''; // Leeren der vorhandenen Liste

    savedNotes.forEach(function(noteText) {
        var newNote = createNoteElement(noteText);
        noteList.appendChild(newNote);
    });
}

// Funktion zum Speichern einer Notiz
function saveNote() {
    var noteText = document.getElementById('noteInput').value.trim();
    if (noteText === '') {
        alert('Bitte geben Sie eine Notiz ein.');
        return;
    }

    var savedNotes = JSON.parse(localStorage.getItem('notes')) || [];
    savedNotes.push(noteText);
    localStorage.setItem('notes', JSON.stringify(savedNotes));

    loadNotes(); // Notizen neu laden
    document.getElementById('noteInput').value = ''; // Notizfeld leeren
}

// Hilfsfunktion zum Erstellen eines Notiz-Listenelements
function createNoteElement(noteText) {
    var newNote = document.createElement('li');
    newNote.innerHTML = `
        <span>${noteText}</span>
        <div>
            <button class="edit-button" onclick="editNote(this)"><i class="bi bi-pencil"></i></button>
            <button class="delete-button" onclick="deleteNote(this)"><i class="bi bi-trash"></i></button>
        </div>
    `;
    return newNote;
}

// Funktion zum Bearbeiten einer Notiz
function editNote(button) {
    var noteSpan = button.parentElement.previousElementSibling;
    var newText = prompt('Bearbeiten Sie die Notiz:', noteSpan.textContent);
    if (newText !== null && newText.trim() !== '') {
        noteSpan.textContent = newText.trim();
        updateSavedNotes();
    }
}

// Funktion zum Löschen einer Notiz
function deleteNote(button) {
    var noteItem = button.parentElement.parentElement;
    var noteText = noteItem.querySelector('span').textContent;

    var savedNotes = JSON.parse(localStorage.getItem('notes')) || [];
    var updatedNotes = savedNotes.filter(function(note) {
        return note !== noteText;
    });
    localStorage.setItem('notes', JSON.stringify(updatedNotes));

    loadNotes(); // Notizen neu laden
}

// Funktion zum Aktualisieren der gespeicherten Notizen in localStorage
function updateSavedNotes() {
    var noteList = document.getElementById('noteList');
    var updatedNotes = [];

    noteList.querySelectorAll('li').forEach(function(noteItem) {
        var noteText = noteItem.querySelector('span').textContent;
        updatedNotes.push(noteText);
    });

    localStorage.setItem('notes', JSON.stringify(updatedNotes));
}

// Funktion zum Anzeigen der App im Vollbildmodus
function launchFullscreen() {
    var element = document.documentElement; // Das gesamte HTML-Dokument auswählen
    if (element.requestFullscreen) {
        element.requestFullscreen(); // Standard-Vollbildmodus-Anfrage
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen(); // Firefox-spezifische Vollbildmodus-Anfrage
    } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen(); // Chrome- und Safari-spezifische Vollbildmodus-Anfrage
    } else if (element.msRequestFullscreen) {
        element.msRequestFullscreen(); // Internet Explorer-spezifische Vollbildmodus-Anfrage
    }
}
