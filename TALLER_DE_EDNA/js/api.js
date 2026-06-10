/**
 * Gestiona login, registro, panel admin y area de usuario via fetch a php/index.php.
 */

const API_BASE = '../php/index.php';

/** Construye la URL de la API */
function buildApiUrl(area) {
    return `${API_BASE}?area=${area}&format=json`;
}

/** Peticion fetch con sesion y parseo JSON */
async function fetchJson(relativePath, options = {}) {
    if (window.location.protocol === 'file:') {
        throw new Error('Abre la web desde http://localhost/TALLER_DE_EDNA/html/Login.html con Apache activo.');
    }

    const response = await fetch(new URL(relativePath, window.location.href).href, {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'fetch',
            ...(options.headers || {}),
        },
        ...options,
    });

    const text = await response.text();
    let data = null;

    if (text !== '') {
        try {
            data = JSON.parse(text);
        } catch (error) {
            const preview = text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 160);
            throw new Error(
                preview
                    ? `Respuesta invalida del servidor: ${preview}`
                    : 'Respuesta invalida del servidor. Comprueba que Apache y MySQL estan activos.'
            );
        }
    }

    return { response, data };
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function createMessageHandlers(noticesEl, errorsEl) {
    return {
        setNotices(messages = []) {
            if (!noticesEl) return;
            noticesEl.innerHTML = messages.map(msg => `<div class="notice">${escapeHtml(msg)}</div>`).join('');
        },
        setErrors(messages = []) {
            if (!errorsEl) return;
            errorsEl.innerHTML = messages.map(msg => `<div class="error">${escapeHtml(msg)}</div>`).join('');
        },
    };
}

function bindTabs(tabs, defaultTab) {
    function showTab(name) {
        Object.keys(tabs).forEach(tab => {
            if (tabs[tab]) {
                tabs[tab].style.display = tab === name ? 'block' : 'none';
            }
        });
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.toggle('active', link.dataset.tab === name);
        });
        window.location.hash = name;
    }

    function getCurrentTab() {
        const hash = window.location.hash.replace('#', '');
        return hash || defaultTab;
    }

    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            showTab(link.dataset.tab);
        });
    });

    window.addEventListener('hashchange', () => showTab(getCurrentTab()));

    return { showTab, getCurrentTab };
}

/** Panel admin:  clientes, talleres y citas. */
function initAdminPanel() {
    const apiUrl = buildApiUrl('admin');
    const { setNotices, setErrors } = createMessageHandlers(
        document.getElementById('notices'),
        document.getElementById('errors')
    );
    const tabs = {
        clientes: document.getElementById('tab-clientes'),
        talleres: document.getElementById('tab-talleres'),
        citas: document.getElementById('tab-citas'),
    };
    const { showTab, getCurrentTab } = bindTabs(tabs, 'clientes');

    const clientForm = document.getElementById('client-form');
    const clientTitle = document.getElementById('client-form-title');
    const clientCancel = document.getElementById('client-cancel');
    const clientesTbody = document.getElementById('clientes-table-body');

    const tallerForm = document.getElementById('taller-form');
    const tallerTitle = document.getElementById('taller-form-title');
    const tallerCancel = document.getElementById('taller-cancel');
    const talleresTbody = document.getElementById('talleres-table-body');

    const citaForm = document.getElementById('cita-form');
    const citaTitle = document.getElementById('cita-form-title');
    const citaCancel = document.getElementById('cita-cancel');
    const citaCliente = document.getElementById('cita_cliente_id');
    const citaTraje = document.getElementById('cita_traje_id');
    const citaTaller = document.getElementById('cita_taller_id');
    const citasTbody = document.getElementById('citas-table-body');

    let adminData = { clientes: [], talleres: [], trajes: [], citas: [] };

    // Carga datos del servidor.
    async function fetchData() {
        try {
            const { response, data } = await fetchJson(apiUrl, { method: 'GET' });

            if (response.status === 401) {
                window.location.href = 'Login.html';
                return;
            }

            if (!response.ok || !data?.ok) {
                setErrors([data?.message || 'No se pudieron cargar los datos.']);
                return;
            }

            adminData = {
                clientes: data.clientes || [],
                talleres: data.talleres || [],
                trajes: data.trajes || [],
                citas: data.citas || [],
            };

            setNotices(data.notices || []);
            setErrors(data.errors || []);
            renderClientes();
            renderTalleres();
            renderCitas();
            populateCitaClienteOptions();
            populateCitaTallerOptions();
            resetForms();
            showTab(getCurrentTab());
        } catch (error) {
            setErrors([error.message || 'No se pudo conectar con el servidor.']);
        }
    }

    function renderClientes() {
        clientesTbody.innerHTML = adminData.clientes.map(cliente => `
            <tr>
                <td>${escapeHtml(cliente.id)}</td>
                <td>${escapeHtml(cliente.nombre)}</td>
                <td>${escapeHtml(cliente.email)}</td>
                <td>${escapeHtml(cliente.superpoder)}</td>
                <td>${escapeHtml(cliente.colores)}</td>
                <td class="actions">
                    <button type="button" data-edit-client="${cliente.id}" class="btn-edit-client">Editar</button>
                    <button type="button" data-delete-client="${cliente.id}" class="btn-delete-client">Eliminar</button>
                </td>
            </tr>
        `).join('');

        clientesTbody.querySelectorAll('.btn-edit-client').forEach(button => {
            button.addEventListener('click', () => startClientEdit(button.dataset.editClient));
        });
        clientesTbody.querySelectorAll('.btn-delete-client').forEach(button => {
            button.addEventListener('click', () => deleteResource('delete_client', { id: button.dataset.deleteClient }));
        });
    }

    function renderTalleres() {
        talleresTbody.innerHTML = adminData.talleres.map(taller => `
            <tr>
                <td>${escapeHtml(taller.id)}</td>
                <td>${escapeHtml(taller.sala)}</td>
                <td>${escapeHtml(taller.tipo)}</td>
                <td class="actions">
                    <button type="button" data-edit-taller="${taller.id}" class="btn-edit-taller">Editar</button>
                    <button type="button" data-delete-taller="${taller.id}" class="btn-delete-taller">Eliminar</button>
                </td>
            </tr>
        `).join('');

        talleresTbody.querySelectorAll('.btn-edit-taller').forEach(button => {
            button.addEventListener('click', () => startTallerEdit(button.dataset.editTaller));
        });
        talleresTbody.querySelectorAll('.btn-delete-taller').forEach(button => {
            button.addEventListener('click', () => deleteResource('delete_taller', { id: button.dataset.deleteTaller }));
        });
    }

    function renderCitas() {
        citasTbody.innerHTML = adminData.citas.map(cita => `
            <tr>
                <td>${escapeHtml(cita.id)}</td>
                <td>${escapeHtml(cita.cliente || '')}</td>
                <td>${escapeHtml(cita.traje || '')}</td>
                <td>${escapeHtml(cita.taller || '')}</td>
                <td>${escapeHtml(cita.dia)}</td>
                <td>${escapeHtml(cita.hora)}</td>
                <td>${escapeHtml(cita.duracion_horas)} h</td>
                <td class="actions">
                    <button type="button" data-delete-cita="${cita.id}" class="btn-delete-cita">Eliminar</button>
                </td>
            </tr>
        `).join('');

        citasTbody.querySelectorAll('.btn-delete-cita').forEach(button => {
            button.addEventListener('click', () => deleteResource('delete_cita', { id: button.dataset.deleteCita }));
        });
    }

    function populateCitaClienteOptions() {
        citaCliente.innerHTML = '<option value="">Selecciona cliente</option>' + adminData.clientes.map(cliente => `
            <option value="${cliente.id}">${escapeHtml(cliente.nombre)}</option>
        `).join('');
    }

    function populateCitaTallerOptions() {
        citaTaller.innerHTML = '<option value="">Selecciona taller</option>' + adminData.talleres.map(taller => `
            <option value="${taller.id}">${escapeHtml(taller.sala)} - ${escapeHtml(taller.tipo)}</option>
        `).join('');
    }

    function populateTrajesForCliente(clienteId, selectedId = '') {
        const options = ['<option value="">Selecciona traje</option>'];
        const trajes = adminData.trajes || [];
        const filtered = clienteId
            ? trajes.filter(traje => String(traje.cliente_id) === String(clienteId))
            : [];
        filtered.forEach(traje => {
            options.push(`<option value="${traje.id}" ${String(traje.id) === String(selectedId) ? 'selected' : ''}>${escapeHtml(traje.nombre)}</option>`);
        });
        citaTraje.innerHTML = options.join('');
    }

    async function deleteResource(action, payload) {
        if (!confirm('¿Seguro que quieres eliminar este elemento?')) return;
        const formData = new FormData();
        formData.append('action', action);
        Object.entries(payload).forEach(([key, value]) => formData.append(key, value));
        await postForm(formData);
    }

    function resetForms() {
        clientForm.reset();
        clientForm.querySelector('[name="action"]').value = 'create_client';
        clientTitle.textContent = 'Nuevo cliente';
        clientCancel.style.display = 'none';

        tallerForm.reset();
        tallerForm.querySelector('[name="action"]').value = 'create_taller';
        tallerTitle.textContent = 'Nuevo taller';
        tallerCancel.style.display = 'none';

        citaForm.reset();
        citaForm.querySelector('[name="action"]').value = 'create_cita';
        citaTitle.textContent = 'Nueva cita';
        citaCancel.style.display = 'none';
        citaTraje.innerHTML = '<option value="">Selecciona traje</option>';
    }

    function startClientEdit(clientId) {
        const cliente = adminData.clientes.find(item => String(item.id) === String(clientId));
        if (!cliente) return;
        clientForm.querySelector('[name="action"]').value = 'update_client';
        document.getElementById('client-id').value = cliente.id;
        document.getElementById('client-nombre').value = cliente.nombre;
        document.getElementById('client-superpoder').value = cliente.superpoder;
        document.getElementById('client-colores').value = cliente.colores;
        clientTitle.textContent = 'Editar cliente';
        clientCancel.style.display = '';
        showTab('clientes');
    }

    function startTallerEdit(tallerId) {
        const taller = adminData.talleres.find(item => String(item.id) === String(tallerId));
        if (!taller) return;
        tallerForm.querySelector('[name="action"]').value = 'update_taller';
        document.getElementById('taller-id').value = taller.id;
        document.getElementById('taller-sala').value = taller.sala;
        document.getElementById('taller-tipo').value = taller.tipo;
        tallerTitle.textContent = 'Editar taller';
        tallerCancel.style.display = '';
        showTab('talleres');
    }

    // Envio de formularios CRUD .
    async function postForm(formData) {
        try {
            const { response, data } = await fetchJson(apiUrl, {
                method: 'POST',
                body: formData,
            });

            if (!response.ok || !data?.ok) {
                setErrors([data?.message || 'Error en la operacion.']);
                return;
            }

            setNotices([data.message || 'Operacion realizada con exito.']);
            await fetchData();
        } catch (error) {
            setErrors([error.message || 'No se pudo conectar con el servidor.']);
        }
    }

    clientForm.addEventListener('submit', event => {
        event.preventDefault();
        postForm(new FormData(clientForm));
    });

    tallerForm.addEventListener('submit', event => {
        event.preventDefault();
        postForm(new FormData(tallerForm));
    });

    citaForm.addEventListener('submit', event => {
        event.preventDefault();
        postForm(new FormData(citaForm));
    });

    clientCancel.addEventListener('click', () => resetForms());
    tallerCancel.addEventListener('click', () => resetForms());
    citaCancel.addEventListener('click', () => resetForms());

    citaCliente.addEventListener('change', () => {
        populateTrajesForCliente(citaCliente.value);
    });

    fetchData();
}

/** Area cliente: estado de trajes y gestion de citas propias. */
function initUsuarioPanel() {
    const apiUrl = buildApiUrl('usuario');
    const { setNotices, setErrors } = createMessageHandlers(
        document.getElementById('notices'),
        document.getElementById('errors')
    );
    const nombreEl = document.getElementById('usuario-nombre');
    const emailEl = document.getElementById('usuario-email');
    const trajesContainer = document.getElementById('trajes-container');
    const citasContainer = document.getElementById('citas-container');
    const tabs = {
        equipo: document.getElementById('tab-equipo'),
        citas: document.getElementById('tab-citas'),
    };
    const { showTab, getCurrentTab } = bindTabs(tabs, 'equipo');

    async function fetchData() {
        try {
            const { response, data } = await fetchJson(apiUrl, { method: 'GET' });

            if (response.status === 401) {
                window.location.href = 'Login.html';
                return;
            }

            if (!response.ok || !data?.ok) {
                setErrors([data?.message || 'No se pudieron cargar los datos.']);
                return;
            }

            setNotices(data.notices || []);
            setErrors(data.errors || []);
            if (nombreEl) nombreEl.textContent = data.cliente?.nombre || 'Usuario';
            if (emailEl) emailEl.textContent = data.cliente?.email || '';
            renderTrajes(data.trajes || [], data.estadoInfo || {});
            renderCitas(data.citas || [], data.trajes || [], data.talleres || []);
        } catch (error) {
            setErrors([error.message || 'No se pudo conectar con el servidor.']);
        }
    }

    function renderTrajes(trajes, estadoInfo) {
        if (!trajes.length) {
            trajesContainer.innerHTML = '<div class="appointment-box"><p>Todavia no tienes trajes asociados a tu ficha de cliente.</p></div>';
            return;
        }

        trajesContainer.innerHTML = `<div class="user-stack">${trajes.map((traje, index) => {
            const info = estadoInfo[traje.estado] || { label: traje.estado || '', text: '' };
            return `
                <details class="status-item" ${index === 0 ? 'open' : ''}>
                    <summary><span>${escapeHtml(traje.nombre)}</span><span class="status-chip ${escapeHtml(traje.estado)}">${escapeHtml(info.label)}</span></summary>
                    <div class="status-copy">${escapeHtml(info.text)}</div>
                </details>
            `;
        }).join('')}</div>`;
    }

    function renderCitas(citas, trajes, talleres) {
        if (citas.length) {
            citasContainer.innerHTML = `<div class="appointment-box"><table class="user-table"><thead><tr><th>Traje</th><th>Taller</th><th>Dia</th><th>Hora</th><th>Duracion</th></tr></thead><tbody>${citas.map(cita => `
                <tr>
                    <td>${escapeHtml(cita.traje || 'Sin traje')}</td>
                    <td>${escapeHtml((cita.sala || 'Sin taller') + ' - ' + (cita.tipo ? cita.tipo.charAt(0).toUpperCase() + cita.tipo.slice(1) : ''))}</td>
                    <td>${escapeHtml(cita.dia)}</td>
                    <td>${escapeHtml(cita.hora)}</td>
                    <td>${escapeHtml(cita.duracion_horas)} h</td>
                </tr>
            `).join('')}</tbody></table><ul class="appointment-notes"><li>Si necesitas mover la cita, de momento debera gestionarlo administracion.</li></ul></div>`;
            return;
        }

        if (!trajes.length) {
            citasContainer.innerHTML = '<div class="appointment-box"><p>No puedes pedir una cita hasta tener al menos un traje asociado.</p></div>';
            return;
        }

        citasContainer.innerHTML = `
            <form id="appointment-form">
                <input type="hidden" name="action" value="create_user_cita">
                <div class="row">
                    <select name="traje_id" required>${['<option value="">Selecciona tu traje</option>'].concat(trajes.map(traje => `<option value="${traje.id}">${escapeHtml(traje.nombre)}</option>`)).join('')}</select>
                    <select name="taller_id" required>${['<option value="">Selecciona taller</option>'].concat(talleres.map(taller => `<option value="${taller.id}">${escapeHtml(taller.sala + ' - ' + (taller.tipo ? taller.tipo.charAt(0).toUpperCase() + taller.tipo.slice(1) : ''))}</option>`)).join('')}</select>
                    <input type="date" name="dia" required>
                    <input type="time" name="hora" min="00:00" max="23:00" step="3600" required>
                    <input type="number" name="duracion_horas" min="1" step="1" value="1" required>
                </div>
                <div class="actions">
                    <button type="submit">Concertar cita</button>
                </div>
            </form>
        `;

        document.getElementById('appointment-form').addEventListener('submit', async event => {
            event.preventDefault();
            await postCita(new FormData(event.currentTarget));
        });
    }

    async function postCita(formData) {
        try {
            const { response, data } = await fetchJson(apiUrl, {
                method: 'POST',
                body: formData,
            });

            if (!response.ok || !data?.ok) {
                setErrors([data?.message || 'Error al crear la cita.']);
                return;
            }

            setNotices([data.message || 'Cita creada correctamente.']);
            await fetchData();
        } catch (error) {
            setErrors([error.message || 'No se pudo conectar con el servidor.']);
        }
    }

    showTab(getCurrentTab());
    fetchData();
}

function initLoginPage() {
    const form = document.getElementById('formLogin');
    const errorBox = document.getElementById('mensajeError');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-passwd');
    const togglePass = document.getElementById('togglePass');
    const iconEye = document.getElementById('icon-eye');
    const iconEyeOff = document.getElementById('icon-eye-off');
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    const savedEmail = params.get('email');

    if (errorBox) {
        if (error) {
            errorBox.textContent = error;
            errorBox.style.display = 'block';
        } else {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
        }
    }

    if (emailInput && savedEmail) {
        emailInput.value = savedEmail;
    }

    if (error || savedEmail) {
        const cleanUrl = window.location.pathname.split('/').pop() || 'Login.html';
        window.history.replaceState({}, document.title, cleanUrl);
    }

    if (togglePass && passwordInput) {
        togglePass.addEventListener('click', () => {
            const show = passwordInput.type === 'password';
            passwordInput.type = show ? 'text' : 'password';
            if (iconEye) iconEye.style.display = show ? 'none' : 'block';
            if (iconEyeOff) iconEyeOff.style.display = show ? 'block' : 'none';
        });
    }

    form?.addEventListener('submit', () => {
        document.getElementById('btnAcceder')?.setAttribute('disabled', 'disabled');
    });
}

/** Validacion de contrasenas  */
function initRegistroPage() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const message = params.get('message');
    const email = params.get('email');
    const box = document.getElementById('form-feedback');
    const form = document.getElementById('form-registro');
    const password = document.getElementById('passwd');
    const passwordConfirm = document.getElementById('passwd-confirm');

    const renderFeedback = (type, html) => {
        if (!box) return;
        box.hidden = false;
        box.classList.remove('is-success', 'is-error');
        box.classList.add(type === 'ok' ? 'is-success' : 'is-error');
        box.innerHTML = html;
    };

    const validatePasswords = () => {
        if (!password || !passwordConfirm) return true;
        if (passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Las contrasenas no coinciden.');
            return false;
        }
        passwordConfirm.setCustomValidity('');
        return true;
    };

    password?.addEventListener('input', validatePasswords);
    passwordConfirm?.addEventListener('input', validatePasswords);

    form?.addEventListener('submit', (event) => {
        if (!validatePasswords()) {
            event.preventDefault();
            renderFeedback('error', '<p>Las contrasenas no coinciden.</p>');
            passwordConfirm.reportValidity();
        }
    });

    if (box && status) {
        let html = message ? `<p>${escapeHtml(message)}</p>` : '';
        if (status === 'ok' && email) {
            html += `<p><strong>Correo de acceso:</strong> ${escapeHtml(email)}</p>`;
            html += '<p>En el login usa ese correo @ednamoda.com.</p>';
        }
        renderFeedback(status, html);
        if (status === 'ok') form?.reset();
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// Inicializa solo el modulo que corresponde a la pagina cargada.
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('formLogin')) {
        initLoginPage();
        return;
    }
    if (document.getElementById('form-registro')) {
        initRegistroPage();
        return;
    }
    if (document.getElementById('client-form')) {
        initAdminPanel();
        return;
    }
    if (document.getElementById('trajes-container')) {
        initUsuarioPanel();
    }
});
