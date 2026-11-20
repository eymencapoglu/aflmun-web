<?php
// includes/footer.php
?>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-left">#SpeakforPeace</div>
            <div class="footer-center">
                <img src="/assets/images/logo.png" alt="AFL MUN Logo">
            </div>
            <div class="footer-right">AFL-MUN'25</div>
        </div>
    </div>
</footer>

<style>
    footer {
        /* Arka plan artık sabit siyah değil, yukarıdaki temayla uyumlu geçişli */
        background: linear-gradient(to top, #000, #0708249c, #0f0f1f, #100f4a98);
        padding: 40px 0 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        position: relative;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.3);
    }

    .container {
        width: 100%;
        max-width: 1200px;
        padding: 0 20px;
        display: flex;
        justify-content: center;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 20px;
        text-align: center;
    }

    .footer-left,
    .footer-right {
        font-size: 3rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.75);
        flex: 1;
        transition: color 0.3s ease;
    }

    .footer-left:hover,
    .footer-right:hover {
        color: rgba(255, 255, 255, 1);
    }

    .footer-center img {
        height: 300px;
        filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.2));
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .footer-center img:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.4));
    }

    /* Mobilde sadece logo görünsün */
    @media (max-width: 1024px) {
        .footer-content {
            flex-direction: column;
            gap: 12px;
        }

        .footer-left,
        .footer-right {
            display: none;
        }

        .footer-center img {
            height: 220px;
        }
    }
</style>
