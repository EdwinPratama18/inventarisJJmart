<template>
    <div class="auth-root">
        <!-- Left: Brand Panel -->
        <div class="auth-panel-brand">
            <div class="brand-content">
                <div class="brand-logo-wrap">
                    <div class="brand-logo-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-name">JJ Mart</div>
                        <div class="brand-tagline">Sistem Inventaris</div>
                    </div>
                </div>

                <div class="brand-headline">
                    <h2>Kelola gudang<br/>dengan presisi.</h2>
                    <p>Monitor stok, lacak transaksi, dan dapatkan notifikasi reorder point secara real-time.</p>
                </div>

                <div class="brand-features">
                    <div class="feature-item">
                        <div class="feature-dot feature-dot--green"></div>
                        <span>Real-time stock monitoring</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot feature-dot--blue"></div>
                        <span>Automatic reorder point alert</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot feature-dot--purple"></div>
                        <span>Laporan inventaris lengkap</span>
                    </div>
                </div>

                <div class="brand-footer-text">
                    &copy; {{ new Date().getFullYear() }} JJ Mart &mdash; Enterprise Inventory System
                </div>
            </div>

            <!-- Decorative circles -->
            <div class="deco-circle deco-circle--1"></div>
            <div class="deco-circle deco-circle--2"></div>
        </div>

        <!-- Right: Form Panel -->
        <div class="auth-panel-form">
            <div class="form-panel-inner">
                <div class="form-heading">
                    <h1>Masuk ke Sistem</h1>
                    <p>Masukkan kredensial akun Anda untuk mengakses dasbor inventaris.</p>
                </div>

                <form @submit.prevent="onMasuk" class="login-form" autocomplete="on" novalidate>
                    <!-- Email -->
                    <div class="field-group">
                        <label class="field-label" for="login-email">Alamat Email</label>
                        <div class="field-input-wrap" :class="{ 'is-error': galat && galat.includes('email') }">
                            <span class="field-prefix-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            <input
                                id="login-email"
                                type="email"
                                class="field-input"
                                v-model="form.email"
                                placeholder="nama@jjmart.id"
                                autocomplete="email"
                                required
                            />
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="field-group">
                        <label class="field-label" for="login-password">Kata Sandi</label>
                        <div class="field-input-wrap">
                            <span class="field-prefix-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input
                                id="login-password"
                                :type="tampilPass ? 'text' : 'password'"
                                class="field-input field-input--has-suffix"
                                v-model="form.password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                            />
                            <button
                                type="button"
                                class="field-suffix-btn"
                                @click="tampilPass = !tampilPass"
                                tabindex="-1"
                                :title="tampilPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            >
                                <Ikon :nama="tampilPass ? 'eye-off' : 'eye'" :ukuran="15" />
                            </button>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <Transition name="alert-slide">
                        <div v-if="toko.galat" class="alert-error" role="alert">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ toko.galat }}</span>
                        </div>
                    </Transition>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn-login"
                        :class="{ 'btn-login--loading': toko.memuat }"
                        :disabled="toko.memuat"
                    >
                        <span v-if="toko.memuat" class="btn-login-content">
                            <span class="spinner"></span>
                            Memverifikasi…
                        </span>
                        <span v-else class="btn-login-content">
                            Masuk ke Sistem
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { gunakanTokoAutentikasi } from '../toko/autentikasi';
import Ikon from '../komponen/Ikon.vue';

const toko = gunakanTokoAutentikasi();
const router = useRouter();

const form = ref({
    email: '',
    password: '',
});
const tampilPass = ref(false);
const galat = ref('');

async function onMasuk() {
    const sukses = await toko.masuk(form.value.email, form.value.password);
    if (sukses) {
        router.push('/dasbor');
    }
}
</script>

<style scoped>
/* ─── Layout ─────────────────────────────────────────── */
.auth-root {
    min-height: 100vh;
    display: flex;
    background: #f8fafc;
}

/* ─── Brand Panel (Left) ──────────────────────────────── */
.auth-panel-brand {
    width: 420px;
    flex-shrink: 0;
    background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 55%, #1d4ed8 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.brand-content {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

.brand-logo-wrap {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}

.brand-logo-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}

.brand-name {
    font-size: 1.15rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.brand-tagline {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.55);
    margin-top: 1px;
    font-weight: 400;
}

.brand-headline h2 {
    font-size: 2.15rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    letter-spacing: -0.03em;
    margin-bottom: 0.9rem;
}

.brand-headline p {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.65;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.835rem;
    color: rgba(255, 255, 255, 0.75);
    font-weight: 400;
}

.feature-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.feature-dot--green  { background: #34d399; box-shadow: 0 0 6px rgba(52, 211, 153, 0.6); }
.feature-dot--blue   { background: #60a5fa; box-shadow: 0 0 6px rgba(96, 165, 250, 0.6); }
.feature-dot--purple { background: #a78bfa; box-shadow: 0 0 6px rgba(167, 139, 250, 0.6); }

.brand-footer-text {
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.3);
    letter-spacing: 0.01em;
}

/* Decorative blobs */
.deco-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    pointer-events: none;
}
.deco-circle--1 {
    width: 320px;
    height: 320px;
    top: -80px;
    right: -120px;
}
.deco-circle--2 {
    width: 180px;
    height: 180px;
    top: 60px;
    right: -30px;
}

/* ─── Form Panel (Right) ──────────────────────────────── */
.auth-panel-form {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    background: #f8fafc;
}

.form-panel-inner {
    width: 100%;
    max-width: 400px;
}

/* Form heading */
.form-heading {
    margin-bottom: 2rem;
}

.form-heading h1 {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.03em;
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.form-heading p {
    font-size: 0.855rem;
    color: #64748b;
    line-height: 1.55;
}

/* Login Form */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    margin-bottom: 0;
}

/* Field */
.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.field-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #334155;
    letter-spacing: 0.005em;
}

.field-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.field-prefix-icon {
    position: absolute;
    left: 0.875rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    pointer-events: none;
    z-index: 1;
}

.field-input {
    width: 100%;
    padding: 0.65rem 0.875rem 0.65rem 2.5rem;
    font-family: inherit;
    font-size: 0.875rem;
    color: #0f172a;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.field-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.field-input::placeholder {
    color: #cbd5e1;
}

.field-input--has-suffix {
    padding-right: 2.5rem;
}

.field-suffix-btn {
    position: absolute;
    right: 0.6rem;
    background: transparent;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: 0.3rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s;
    line-height: 0;
}
.field-suffix-btn:hover { color: #475569; }

/* Error Alert */
.alert-error {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.7rem 0.875rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #dc2626;
    font-size: 0.815rem;
    line-height: 1.4;
}

.alert-slide-enter-active,
.alert-slide-leave-active {
    transition: all 0.2s ease;
}
.alert-slide-enter-from,
.alert-slide-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

/* Submit Button */
.btn-login {
    width: 100%;
    padding: 0.72rem 1rem;
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, box-shadow 0.15s, transform 0.1s;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.28);
    margin-top: 0.25rem;
}

.btn-login:hover:not(:disabled) {
    background: #1d4ed8;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.36);
    transform: translateY(-1px);
}

.btn-login:active:not(:disabled) {
    transform: translateY(0);
}

.btn-login:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.btn-login-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* Spinner */
.spinner {
    width: 15px;
    height: 15px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    flex-shrink: 0;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ─── Responsive ──────────────────────────────────────── */
@media (max-width: 768px) {
    .auth-panel-brand {
        display: none;
    }
    .auth-panel-form {
        background: #ffffff;
    }
}
</style>
