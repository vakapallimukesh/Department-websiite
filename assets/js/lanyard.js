// Interactive 3D Lanyard Badge Physics Engine for SRKREC Department Website
class LanyardBadge {
    constructor(canvasId) {
        this.canvas = typeof canvasId === 'string' ? document.getElementById(canvasId) : canvasId;
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext('2d');
        this.width = this.canvas.parentElement ? this.canvas.parentElement.clientWidth : 800;
        this.height = 420;
        this.canvas.width = this.width;
        this.canvas.height = this.height;

        this.anchorX = this.width / 2;
        this.anchorY = 20;
        this.cardWidth = 190;
        this.cardHeight = 270;
        
        this.angle = 0;
        this.angularVel = 0;
        this.isDragging = false;
        this.mouseX = 0;
        this.mouseY = 0;

        this.initEvents();
        this.animate();
    }

    initEvents() {
        const onMove = (e) => {
            const rect = this.canvas.getBoundingClientRect();
            const mx = e.clientX - rect.left;
            const my = e.clientY - rect.top;
            
            if (this.isDragging) {
                this.angle = (mx - this.anchorX) * 0.003;
                this.angularVel = (mx - this.mouseX) * 0.001;
            } else {
                const dx = mx - this.anchorX;
                if (Math.abs(dx) < 300) {
                    this.angularVel += dx * 0.00004;
                }
            }
            this.mouseX = mx;
            this.mouseY = my;
        };

        this.canvas.addEventListener('mousemove', onMove);
        this.canvas.addEventListener('mousedown', () => this.isDragging = true);
        window.addEventListener('mouseup', () => this.isDragging = false);
        window.addEventListener('resize', () => {
            if (this.canvas.parentElement) {
                this.width = this.canvas.parentElement.clientWidth;
                this.canvas.width = this.width;
                this.anchorX = this.width / 2;
            }
        });
    }

    animate() {
        const ctx = this.ctx;
        ctx.clearRect(0, 0, this.width, this.height);

        // Pendulum Spring Physics
        const gravity = 0.0025;
        const damping = 0.965;
        
        this.angularVel -= Math.sin(this.angle) * gravity;
        this.angularVel *= damping;
        this.angle += this.angularVel;

        const stringLength = 170;
        const cardCenterX = this.anchorX + Math.sin(this.angle) * stringLength;
        const cardCenterY = this.anchorY + Math.cos(this.angle) * stringLength;

        // Draw Lanyard Woven Ribbon Straps
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(this.anchorX - 16, 0);
        ctx.quadraticCurveTo(this.anchorX - 8, this.anchorY + 40, cardCenterX - 8, cardCenterY - 24);
        ctx.strokeStyle = '#7c3aed';
        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(this.anchorX + 16, 0);
        ctx.quadraticCurveTo(this.anchorX + 8, this.anchorY + 40, cardCenterX + 8, cardCenterY - 24);
        ctx.strokeStyle = '#2563eb';
        ctx.lineWidth = 12;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Lanyard Metal Clip Ring
        ctx.beginPath();
        ctx.arc(cardCenterX, cardCenterY - 18, 9, 0, Math.PI * 2);
        ctx.fillStyle = '#cbd5e1';
        ctx.fill();
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#475569';
        ctx.stroke();

        // Transform for 3D Swing
        ctx.translate(cardCenterX, cardCenterY);
        ctx.rotate(this.angle * 0.85);

        // Card Drop Shadow
        ctx.shadowColor = 'rgba(0, 0, 0, 0.15)';
        ctx.shadowBlur = 20;
        ctx.shadowOffsetY = 12;

        // Card Base Body
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.roundRect(-this.cardWidth / 2, -15, this.cardWidth, this.cardHeight, 18);
        ctx.fill();

        ctx.shadowColor = 'transparent';
        ctx.lineWidth = 1.5;
        ctx.strokeStyle = 'rgba(124, 58, 237, 0.3)';
        ctx.stroke();

        // Card Header Gradient Bar
        const grad = ctx.createLinearGradient(-this.cardWidth / 2, 0, this.cardWidth / 2, 0);
        grad.addColorStop(0, '#7c3aed');
        grad.addColorStop(1, '#2563eb');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.roundRect(-this.cardWidth / 2, -15, this.cardWidth, 58, [18, 18, 0, 0]);
        ctx.fill();

        // Header Title
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 12px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('SRKREC CSD INCUBATOR', 0, 18);

        // Avatar Outer Circle
        ctx.beginPath();
        ctx.arc(0, 80, 36, 0, Math.PI * 2);
        ctx.fillStyle = '#f1f5f9';
        ctx.fill();
        ctx.strokeStyle = '#7c3aed';
        ctx.lineWidth = 3;
        ctx.stroke();

        // Emoji Rocket Badge Icon
        ctx.font = '28px sans-serif';
        ctx.fillText('🚀', 0, 90);

        // Card Body Text
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 16px Inter, sans-serif';
        ctx.fillText('STARTUP VENTURE', 0, 140);

        ctx.fillStyle = '#64748b';
        ctx.font = '12px Inter, sans-serif';
        ctx.fillText('ID: CSD-INCU-2026', 0, 164);

        // Status Badge Pill
        ctx.fillStyle = '#dcfce7';
        ctx.beginPath();
        ctx.roundRect(-55, 185, 110, 24, 12);
        ctx.fill();
        
        ctx.fillStyle = '#15803d';
        ctx.font = 'bold 11px Inter, sans-serif';
        ctx.fillText('● VERIFIED INCUBATED', 0, 201);

        // Card Slot Hole
        ctx.beginPath();
        ctx.roundRect(-14, -8, 28, 7, 3.5);
        ctx.fillStyle = '#94a3b8';
        ctx.fill();

        ctx.restore();

        requestAnimationFrame(() => this.animate());
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('lanyardCanvas');
    if (canvas) {
        new LanyardBadge(canvas);
    }
});
