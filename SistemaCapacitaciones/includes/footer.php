<!-- ==========================================
     FOOTER FUTURISTA CON TRANSICIÓN
=========================================== -->
<style>
.footer-futuristic {
    background: rgba(0,0,0,0.85);
    padding: 40px 20px;
    margin-top: 60px;
    text-align: center;
    color: #fff;
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255,255,255,0.15);

    opacity: 0;
    transform: translateY(40px);
    animation: footerReveal 1.2s ease-out forwards;
}

@keyframes footerReveal {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Tarjetas de autores */
.footer-authors {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: wrap;
    margin-top: 25px;
}

.footer-card {
    background: rgba(255,255,255,0.05);
    padding: 15px 25px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    color: #e3e3e3;
    min-width: 220px;

    opacity: 0;
    transform: translateY(25px);
    animation: footerCard 1s ease-out forwards;
}

.footer-card:nth-child(1) { animation-delay: .3s; }
.footer-card:nth-child(2) { animation-delay: .45s; }
.footer-card:nth-child(3) { animation-delay: .6s; }
.footer-card:nth-child(4) { animation-delay: .75s; }

@keyframes footerCard {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<footer class="footer-futuristic">
    <h4>Desarrollado por:</h4>

    <div class="footer-authors">
        <div class="footer-card">Acosta Falcon, Marco</div>
        <div class="footer-card">Aliaga Salas, Manuel</div>
        <div class="footer-card">Colqui Guillermo, Victor</div>
        <div class="footer-card">Solorzano Moya, Crilmer</div>
    </div>

    <p style="margin-top:25px;opacity:0.7;">
        © <?= date("Y") ?> Sistema de Capacitaciones – Todos los derechos reservados.
    </p>
</footer>

</body>
</html>
