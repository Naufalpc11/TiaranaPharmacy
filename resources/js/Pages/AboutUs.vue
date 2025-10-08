<template>
    <MainLayout>
        <div class="about-us-container">
            <div class="about-header">
                <h1>Tentang Kami</h1>
            </div>

            <div class="about-content">
                <div class="about-section">
                    <p>Apotek Tiarana Farma adalah apotek modern yang berkomitmen untuk memberikan pelayanan kesehatan terbaik bagi masyarakat. Dengan dukungan tim profesional dan fasilitas modern, kami siap melayani kebutuhan kesehatan Anda.</p>
                </div>
            </div>

            <div class="history-section">
                <div class="history-content">
                    <h2>Sejarah Kami</h2>
                    <p>Berdiri sejak tahun 2021, Apotek Tiarana Farma telah melayani ribuan pelanggan dengan dedikasi tinggi dalam memberikan layanan kesehatan. Berawal dari sebuah apotek kecil, kini kami telah berkembang menjadi apotek modern yang dilengkapi dengan berbagai fasilitas untuk memenuhi kebutuhan kesehatan masyarakat.</p>
                </div>
            </div>

            <div class="team-section">
                <h2>Tim Kami</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="/images/team/pharmacist1.jpg" alt="Apoteker">
                        <h3>Dr. Sarah Johnson</h3>
                        <p>Apoteker Penanggung Jawab</p>
                    </div>
                    <div class="team-member">
                        <img src="/images/team/pharmacist2.jpg" alt="Asisten Apoteker">
                        <h3>David Wilson</h3>
                        <p>Asisten Apoteker</p>
                    </div>
                    <div class="team-member">
                        <img src="/images/team/pharmacist3.jpg" alt="Staff">
                        <h3>Emily Brown</h3>
                        <p>Staff Farmasi</p>
                    </div>
                </div>
            </div>

            <section class="location-section" id="lokasi">
                <h2>Lokasi Kami</h2>
                <div class="location-content">
                    <div class="location-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h3>Alamat</h3>
                                <p>Jl. Sehat No. 10, [Nama Kota]</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <h3>Telepon</h3>
                                <p>0812-3456-7890</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <h3>Jam Buka</h3>
                                <p>Senin - Sabtu: 08:00 - 21:00</p>
                                <p>Minggu: 09:00 - 20:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8714503220267!2d116.9010663745559!3d-1.2482881355849063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df145cac72eb3bf%3A0x16844957779a9566!2sApotek%20Tiarana%20Farma!5e0!3m2!1sid!2sid!4v1759721919148!5m2!1sid!2sid"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </section>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
</script>

<style lang="scss" scoped>
@import '../../css/AboutUs.scss';
</style>
