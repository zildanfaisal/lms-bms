import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

// Global flash notifications from session (injected into window.FLASH_MESSAGES by layout)
document.addEventListener('DOMContentLoaded', () => {
	const flashes = window.FLASH_MESSAGES || {};
	const show = (type, text) => {
		if (!text) return;
		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: type,
			title: text,
			showConfirmButton: false,
			timer: 3500,
			timerProgressBar: true
		});
	};
	show('success', flashes.success);
	show('success', flashes.status); // treat status as success
	show('error', flashes.error);
	show('warning', flashes.warning);
	show('info', flashes.info);

	// Logout confirmation (forms with data-logout-confirm)
	document.querySelectorAll('form[data-logout-confirm]').forEach(form => {
		form.addEventListener('submit', (e) => {
			if (form._confirmed) return; // already confirmed
			e.preventDefault();
			Swal.fire({
				title: 'Yakin logout?',
				text: 'Sesi Anda akan diakhiri.',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Logout',
				cancelButtonText: 'Batal',
				confirmButtonColor: '#d33'
			}).then(res => {
				if (res.isConfirmed) {
					form._confirmed = true;
					form.submit();
				}
			});
		});
	});

	async function runPrecheckIfAny(form, type) {
		const precheckUrl = form.getAttribute('data-precheck');
		if (!precheckUrl) return { ok: true };
		try {
			let url = precheckUrl;
			// For role update, pass selected role if present
			const roleSel = form.querySelector('select[name="role"]');
			if (roleSel) {
				const u = new URL(url, window.location.origin);
				u.searchParams.set('role', roleSel.value || '');
				url = u.toString();
			}
			const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
			if (!res.ok) return { ok: false, message: 'Gagal memeriksa prasyarat aksi.' };
			const data = await res.json();
			return data;
		} catch (err) {
			console.error('Precheck error', err);
			return { ok: false, message: 'Terjadi kesalahan saat precheck.' };
		}
	}

	// Delegated listener for delete/update confirmations to support dynamically injected forms
	document.addEventListener('submit', async (e) => {
		const form = e.target.closest('form');
		if (!form || form._confirmed) return;

		// Delete-like confirmations
		if (form.matches('form[data-confirm]')) {
			e.preventDefault();
			const pre = await runPrecheckIfAny(form, 'delete');
			if (!pre.ok) {
				Swal.fire({ icon: 'error', title: 'Tidak bisa lanjut', text: pre.message || 'Validasi gagal.' });
				return;
			}
			const msg = pre.message || form.getAttribute('data-confirm') || 'Yakin melakukan aksi ini?';
			Swal.fire({
				title: msg,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya',
				cancelButtonText: 'Batal',
				confirmButtonColor: '#d33'
			}).then(res => {
				if (res.isConfirmed) {
					form._confirmed = true;
					form.submit();
				}
			});
			return;
		}

		// Update-like confirmations
		if (form.matches('form[data-update-confirm]')) {
			e.preventDefault();
			const pre = await runPrecheckIfAny(form, 'update');
			if (!pre.ok) {
				Swal.fire({ icon: 'error', title: 'Tidak bisa lanjut', text: pre.message || 'Validasi gagal.' });
				return;
			}
			const unlockAttempt = form.querySelector('input[name="is_locked"]');
			const wasLocked = unlockAttempt && unlockAttempt.defaultChecked === true; // initial state
			const nowLocked = unlockAttempt && unlockAttempt.checked === true;
			const unlocking = wasLocked && !nowLocked;
			const unlockMsg = form.getAttribute('data-unlock-confirm') || 'Buka kunci item ini?';
			const updateMsg = pre.message || form.getAttribute('data-update-confirm') || 'Simpan perubahan?';
			const msg = unlocking ? unlockMsg : updateMsg;
			Swal.fire({
				title: msg,
				icon: unlocking ? 'question' : 'info',
				showCancelButton: true,
				confirmButtonText: unlocking ? 'Unlock' : 'Simpan',
				cancelButtonText: 'Batal',
			}).then(res => {
				if (res.isConfirmed) {
					form._confirmed = true;
					form.submit();
				}
			});
			return;
		}
	}, true);

	// Show login failure via errors bag (if route is login) assuming a login form with id="login-form"
	const loginForm = document.getElementById('login-form');
	if (loginForm && document.querySelector('.login-errors')) {
		Swal.fire({
			icon: 'error',
			title: 'Login gagal',
			text: 'Periksa email / password Anda',
		});
	}
});

