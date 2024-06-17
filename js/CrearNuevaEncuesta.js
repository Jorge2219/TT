function CrearNuevaEncuesta() {
    let questionsHtml = '<input id="swal-input1" class="swal2-input" placeholder="Título de la encuesta">';
    questionsHtml += '<input id="swal-input2" class="swal2-input" placeholder="Descripción de la encuesta">';
    questionsHtml += '<div id="questions-container"></div>';
    questionsHtml += '<button id="addQuestionBtn" class="swal2-confirm swal2-styled">Agregar Pregunta</button>';

    Swal.fire({
        title: 'Crear Encuesta',
        html: questionsHtml,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        customClass: {
            popup: 'custom-swal-dialog'
        },
        preConfirm: () => {
            // Obtener los datos de la encuesta
            const title = document.getElementById('swal-input1').value;
            const description = document.getElementById('swal-input2').value;
            const questions = [];
            document.querySelectorAll('.swal2-question-input').forEach(input => {
                questions.push(input.value);
            });

            // Mostrar la vista previa de la encuesta
            MostrarVistaPrevia(title, description, questions);

            // Evitar que SweetAlert cierre automáticamente
            return false;
        }
    });

    // Agregar evento para el botón de agregar pregunta
    document.getElementById('addQuestionBtn').addEventListener('click', AgregarPregunta);
}

function AgregarPregunta() {
    const questionsContainer = document.getElementById('questions-container');
    const questionInput = document.createElement('input');
    questionInput.classList.add('swal2-input', 'swal2-question-input');
    questionInput.placeholder = 'Pregunta';
    questionsContainer.appendChild(questionInput);
}

function MostrarVistaPrevia(title, description, questions) {
    let previewHtml = '<h2>Previsualización de la Encuesta</h2>';
    previewHtml += `<h3>${title}</h3>`;
    previewHtml += `<p>${description}</p>`;
    previewHtml += '<h4>Preguntas:</h4>';
    previewHtml += '<table id="previewTable">';
    questions.forEach((question, index) => {
        previewHtml += `
            <tr>
                <td>Pregunta ${index + 1}:</td>
                <td>${question}</td>
            </tr>
        `;
    });
    previewHtml += '</table>';

    Swal.fire({
        title: 'Vista Previa',
        html: previewHtml,
        confirmButtonText: 'Confirmar',
        showCancelButton: true,
        cancelButtonText: 'Editar',
        showLoaderOnConfirm: true,
        customClass: {
            popup: 'custom-swal-dialog'
        }
    }).then((result) => {
        // Si el usuario confirma, continuar con la creación de la encuesta
        if (result.isConfirmed) {
            GuardarEncuesta(title, description, questions);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Si el usuario desea editar, permitir la edición de la encuesta
            CrearNuevaEncuesta();
        }
    });
}

// Función para actualizar el título con el texto ingresado por el usuario
document.getElementById('tituloInput').addEventListener('input', function() {
    document.getElementById('titulo').textContent = this.value || 'Encuesta de satisfacción a estudiantes';
});

