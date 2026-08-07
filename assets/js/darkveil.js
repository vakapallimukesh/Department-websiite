// DarkVeil WebGL Shader Engine
// ReactBits DarkVeil Component Port for Vanilla JS
class DarkVeilShader {
    constructor(canvas, options = {}) {
        this.canvas = typeof canvas === 'string' ? document.getElementById(canvas) : canvas;
        if (!this.canvas) return;

        this.gl = this.canvas.getContext('webgl') || this.canvas.getContext('experimental-webgl');
        if (!this.gl) {
            console.warn('WebGL not supported for DarkVeil');
            return;
        }

        this.hueShift = options.hueShift || 0;
        this.speed = options.speed || 0.5;
        this.warpAmount = options.warpAmount || 0;
        
        this.init();
    }

    init() {
        const gl = this.gl;
        
        const vsSource = `
            attribute vec2 position;
            void main() {
                gl_Position = vec4(position, 0.0, 1.0);
            }
        `;

        const fsSource = `
            precision mediump float;
            uniform vec2 uResolution;
            uniform float uTime;
            uniform float uHueShift;

            mat3 rgb2yiq = mat3(0.299, 0.587, 0.114, 0.596, -0.274, -0.322, 0.211, -0.523, 0.312);
            mat3 yiq2rgb = mat3(1.0, 0.956, 0.621, 1.0, -0.272, -0.647, 1.0, -1.106, 1.703);

            vec3 hueShiftRGB(vec3 col, float deg) {
                vec3 yiq = rgb2yiq * col;
                float rad = radians(deg);
                float cosh = cos(rad), sinh = sin(rad);
                vec3 yiqShift = vec3(yiq.x, yiq.y * cosh - yiq.z * sinh, yiq.y * sinh + yiq.z * cosh);
                return clamp(yiq2rgb * yiqShift, 0.0, 1.0);
            }

            void main() {
                vec2 uv = gl_FragCoord.xy / uResolution.xy * 2.0 - 1.0;
                uv.x *= uResolution.x / uResolution.y;
                float t = uTime * 0.5;
                float r = length(uv);

                vec3 col = vec3(
                    0.09 + 0.06 * sin(uv.x * 2.5 + t + r * 3.0),
                    0.05 + 0.04 * cos(uv.y * 2.5 + t * 0.8),
                    0.16 + 0.09 * sin(r * 4.0 - t * 0.6)
                );

                col = hueShiftRGB(col, uHueShift);
                gl_FragColor = vec4(clamp(col, 0.0, 1.0), 0.95);
            }
        `;

        this.program = this.createProgram(vsSource, fsSource);
        this.positionLoc = gl.getAttribLocation(this.program, "position");
        this.resLoc = gl.getUniformLocation(this.program, "uResolution");
        this.timeLoc = gl.getUniformLocation(this.program, "uTime");
        this.hueLoc = gl.getUniformLocation(this.program, "uHueShift");

        this.resize();
        window.addEventListener('resize', () => this.resize());
        this.startTime = performance.now();
        this.animate();
    }

    createShader(type, source) {
        const gl = this.gl;
        const shader = gl.createShader(type);
        gl.shaderSource(shader, source);
        gl.compileShader(shader);
        return shader;
    }

    createProgram(vs, fs) {
        const gl = this.gl;
        const program = gl.createProgram();
        gl.attachShader(program, this.createShader(gl.VERTEX_SHADER, vs));
        gl.attachShader(program, this.createShader(gl.FRAGMENT_SHADER, fs));
        gl.linkProgram(program);
        return program;
    }

    resize() {
        const gl = this.gl;
        const width = window.innerWidth;
        const height = window.innerHeight;
        this.canvas.width = width;
        this.canvas.height = height;
        gl.viewport(0, 0, width, height);
    }

    animate() {
        const gl = this.gl;
        const time = (performance.now() - this.startTime) / 1000;

        gl.useProgram(this.program);
        gl.enableVertexAttribArray(this.positionLoc);
        
        const buffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([
            -1, -1, 1, -1, -1, 1,
            -1, 1, 1, -1, 1, 1
        ]), gl.STATIC_DRAW);
        gl.vertexAttribPointer(this.positionLoc, 2, gl.FLOAT, false, 0, 0);

        gl.uniform2f(this.resLoc, this.canvas.width, this.canvas.height);
        gl.uniform1f(this.timeLoc, time * this.speed);
        gl.uniform1f(this.hueLoc, this.hueShift);

        gl.drawArrays(gl.TRIANGLES, 0, 6);
        requestAnimationFrame(() => this.animate());
    }
}

// Auto-initialize DarkVeil canvas if present
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('darkVeilCanvas') || document.getElementById('particleCanvas');
    if (canvas) {
        new DarkVeilShader(canvas, { speed: 0.5, hueShift: 0 });
    }
});
