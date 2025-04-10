document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const mainContent = document.getElementById('mainContent');
    const addUserBtn = document.getElementById('addUserBtn');
    const userModal = document.getElementById('userModal');
    const confirmModal = document.getElementById('confirmModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const userForm = document.getElementById('userForm');
    const deleteForm = document.getElementById('deleteForm');
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');

    let isSidebarVisible = window.innerWidth > 768;

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            isSidebarVisible = !isSidebarVisible;
            sidebar.classList.toggle('visible');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    }

    function handleResponsive() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('visible');
            toggleBtn.style.display = 'flex';
            mainContent.style.marginLeft = '0';
        } else {
            toggleBtn.style.display = 'none';
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '70px';
            } else {
                mainContent.style.marginLeft = '250px';
            }
        }
    }

    function openUserModal(type, userId = null) {
        const modalTitle = document.getElementById('modalTitle');
        const formAction = document.getElementById('formAction');
        const userIdInput = document.getElementById('userId');

        if (type === 'add') {
            modalTitle.textContent = 'Agregar Nuevo Usuario';
            formAction.value = 'add';
            userIdInput.value = '';
            userForm.reset();
        } else {
            modalTitle.textContent = 'Editar Usuario';
            formAction.value = 'edit';
            userIdInput.value = userId;

            const userRow = document.querySelector(`tr[data-id="${userId}"]`);
            if (userRow) {
                document.getElementById('username').value = userRow.cells[1].textContent;
                document.getElementById('email').value = userRow.cells[2].textContent;
                document.getElementById('role').value = userRow.querySelector('.role-badge').textContent.trim().toLowerCase();
            }
        }

            

        userModal.classList.add('active');
    }

    function openConfirmModal(userId) {
        document.getElementById('deleteId').value = userId;
        confirmModal.classList.add('active');
    }

    function closeModals() {
        userModal.classList.remove('active');
        confirmModal.classList.remove('active');
    }

    function sortTable(columnIndex) {
        const table = document.getElementById('usersTable');
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const header = table.querySelectorAll('thead th')[columnIndex];
        const isAscending = !header.classList.contains('asc');

        table.querySelectorAll('thead th').forEach(th => th.classList.remove('asc', 'desc'));

        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.trim();
            const bValue = b.cells[columnIndex].textContent.trim();

            if (columnIndex === 0) {
                return isAscending ? parseInt(aValue) - parseInt(bValue) : parseInt(bValue) - parseInt(aValue);
            } else {
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            }
        });

        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));

        header.classList.add(isAscending ? 'asc' : 'desc');
    }

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');

        rows.forEach(row => {
            const username = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const role = row.querySelector('.role-badge').textContent.toLowerCase();

            const matchesSearch = username.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleValue || role.includes(roleValue);

            row.style.display = matchesSearch && matchesRole ? '' : 'none';
        });
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    closeBtn.addEventListener('click', toggleSidebar);
    window.addEventListener('resize', handleResponsive);

    document.addEventListener('click', function (e) {
        if (e.target.closest('.edit')) {
            const userId = e.target.closest('.edit').getAttribute('data-id');
            openUserModal('edit', userId);
        }

        if (e.target.closest('.delete')) {
            const userId = e.target.closest('.delete').getAttribute('data-id');
            openConfirmModal(userId);
        }
    });

    document.querySelectorAll('[data-column]').forEach(icon => {
        icon.addEventListener('click', function () {
            const columnIndex = parseInt(this.getAttribute('data-column'));
            sortTable(columnIndex);
        });
    });

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);

    addUserBtn.addEventListener('click', () => openUserModal('add'));
    closeModalBtn.addEventListener('click', closeModals);
    cancelBtn.addEventListener('click', closeModals);
    cancelDeleteBtn.addEventListener('click', closeModals);

    window.addEventListener('click', function (e) {
        if (e.target === userModal || e.target === confirmModal) {
            closeModals();
        }
    });

    handleResponsive();
});
