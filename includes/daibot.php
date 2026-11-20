<?php
/**
 * DaiBot: asistente de preguntas frecuentes para PetCare.
 * No consulta la base de datos ni envía información a servidores externos.
 * Incluir una sola vez desde index.php, antes del footer.
 */
?>
<style>
#daibot-widget, #daibot-widget * {box-sizing:border-box}
#daibot-widget{position:fixed;right:18px;bottom:62px;z-index:9999;font-family:Arial,Helvetica,sans-serif;color:#553347}
#daibot-toggle{border:0;background:transparent;display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;padding:4px;filter:drop-shadow(0 3px 5px #0002);animation:dai-float 3s ease-in-out infinite}
#daibot-toggle:focus-visible,#daibot-close:focus-visible,#daibot-send:focus-visible,#daibot-contact:focus-visible{outline:3px solid #81385f;outline-offset:3px}
.dai-face-icon{display:inline-flex;flex:none;align-items:center;justify-content:center;width:36px;height:36px;border:2px solid #c94783;background:#f9c1da;border-radius:14px;position:relative;vertical-align:middle}
.dai-face-icon:before{content:'♥';position:absolute;top:-16px;left:12px;color:#d94784;font-size:17px;line-height:1}
.dai-face-icon:after{content:'';position:absolute;top:-5px;left:17px;height:6px;border-left:2px solid #c94783}
.dai-screen{display:flex;align-items:center;justify-content:space-evenly;width:26px;height:22px;border-radius:9px;background:#fffafd}
.dai-eye{width:5px;height:7px;border-radius:50%;background:#523346;animation:dai-blink 5s infinite}
.dai-robot-large{width:67px;height:67px;border-radius:24px;border-width:3px;position:relative}
.dai-robot-large:before{font-size:23px;top:-22px;left:24px}
.dai-robot-large:after{height:10px;top:-8px;left:32px;border-left-width:3px}
.dai-robot-large .dai-screen{width:49px;height:39px;border-radius:16px}
.dai-robot-large .dai-eye{width:8px;height:11px}
.dai-paw{position:absolute;bottom:-11px;left:22px;background:#f9c1da;border:2px solid #c94783;border-radius:10px;padding:1px 5px;font-size:13px}
.dai-arm{position:absolute;right:-13px;bottom:9px;width:18px;height:9px;background:#e879a8;border:2px solid #c94783;border-radius:9px;transform-origin:left center;animation:dai-wave 2.4s ease-in-out infinite}
.dai-name{background:#e879a8;color:white;border-radius:20px;padding:6px 15px;font-size:14px;font-weight:bold;margin-top:11px}
#daibot-hint{position:absolute;bottom:64px;right:95px;width:175px;background:#fff5fa;border:1px solid #edb4ce;border-radius:14px;padding:10px;font-size:13px;text-align:center;box-shadow:0 4px 14px #0002;display:none}
#daibot-panel{position:absolute;right:0;bottom:113px;width:min(350px,calc(100vw - 24px));height:min(420px,calc(100dvh - 210px));min-height:240px;background:#fff;border:2px solid #e879a8;border-radius:18px;overflow:hidden;box-shadow:0 8px 24px #0003;display:none;flex-direction:column}
#daibot-header{display:flex;align-items:center;gap:9px;background:#e879a8;color:#fff;padding:10px 12px;flex:none}
#daibot-header .dai-face-icon{width:30px;height:30px;transform:scale(.8);margin:0 2px}
#daibot-header strong{flex:1;font-size:15px}
#daibot-close{border:0;background:transparent;color:white;font-size:24px;cursor:pointer;padding:0 4px}
#daibot-messages{flex:1;min-height:0;overflow-y:auto;padding:13px 10px;background:#fff8fb;display:flex;flex-direction:column;gap:12px}
.dai-line{display:flex;align-items:flex-end;gap:9px;max-width:100%;animation:dai-appear .25s ease-out}
.dai-line-user{justify-content:flex-end}
.dai-line .dai-face-icon{margin:0 0 3px 3px}
.dai-bubble{font-size:14px;line-height:1.4;white-space:pre-wrap;overflow-wrap:anywhere;padding:10px 12px;border-radius:14px;max-width:calc(100% - 54px);background:#f8dce9;color:#553347}
.dai-line-user .dai-bubble{background:#e879a8;color:#fff;max-width:86%}
.dai-typing .dai-bubble{font-style:italic;opacity:.85}
.dai-typing .dai-face-icon{animation:dai-nod .6s ease-in-out infinite alternate}
#daibot-form{display:flex;gap:7px;padding:9px;background:#fff;flex:none}
#daibot-input{min-width:0;flex:1;border:1px solid #dfadc4;border-radius:9px;padding:9px;font-size:14px}
#daibot-send{border:0;background:#e879a8;color:#fff;border-radius:9px;padding:8px 12px;cursor:pointer}
#daibot-contact{display:block;flex:none;margin:0 9px 9px;padding:9px;background:#fff0f6;border:1px solid #efbdd2;color:#94365f;text-align:center;border-radius:9px;text-decoration:none;font-size:13px;font-weight:bold}
#daibot-copy{display:block;margin:0 9px 9px;padding:7px;border:1px solid #e9bad0;border-radius:9px;background:white;color:#94365f;cursor:pointer;width:calc(100% - 18px);font-size:12px}
.dai-typing-dots{display:inline-block;width:0;white-space:nowrap;letter-spacing:2px;animation:dai-dots 1.1s steps(4,end) infinite;overflow:hidden;vertical-align:bottom}
@keyframes dai-dots{0%{width:0}100%{width:24px}}
@keyframes dai-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
@keyframes dai-wave{0%,100%{transform:rotate(12deg)}50%{transform:rotate(-30deg)}}
@keyframes dai-blink{0%,94%,100%{transform:scaleY(1)}96%{transform:scaleY(.12)}}
@keyframes dai-appear{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@keyframes dai-nod{from{transform:rotate(-7deg)}to{transform:rotate(7deg)}}
@keyframes dai-jump{0%,100%{transform:translateY(0)}50%{transform:translateY(-16px)}}
#daibot-toggle.dai-excited{animation:dai-jump .42s ease-in-out 2}
@media(max-width:480px){#daibot-widget{right:10px;bottom:40px}#daibot-panel{bottom:110px;height:min(400px,calc(100dvh - 170px))}#daibot-hint{right:85px;width:145px}}
@media(prefers-reduced-motion:reduce){#daibot-widget *,#daibot-widget *:before,#daibot-widget *:after{animation:none!important}}
</style>
<div id="daibot-widget">
  <div id="daibot-hint" role="status">¡Hola! ¿Necesitás ayuda con PetCare?</div>
  <button id="daibot-toggle" type="button" aria-label="Abrir DaiBot" aria-expanded="false" aria-controls="daibot-panel">
    <span class="dai-face-icon dai-robot-large" aria-hidden="true"><span class="dai-screen"><span class="dai-eye"></span><span class="dai-eye"></span></span><span class="dai-paw">🐾</span><span class="dai-arm"></span></span>
    <span class="dai-name">DaiBot</span>
  </button>
  <section id="daibot-panel" aria-label="Chat de DaiBot">
    <div id="daibot-header"><span class="dai-face-icon" aria-hidden="true"><span class="dai-screen"><span class="dai-eye"></span><span class="dai-eye"></span></span></span><strong>DaiBot | Asistente de PetCare</strong><button type="button" id="daibot-close" aria-label="Cerrar DaiBot">×</button></div>
    <div id="daibot-messages" role="log" aria-live="polite"></div>
    <form id="daibot-form"><input id="daibot-input" type="text" maxlength="300" placeholder="Escribí tu consulta..." aria-label="Tu consulta" required autocomplete="off"><button id="daibot-send" type="submit">Enviar</button></form>
    <a id="daibot-contact" href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=profedaianapc%40gmail.com&amp;su=Consulta%20desde%20PetCare%20-%20DaiBot" target="_blank" rel="noopener noreferrer">✉️ Escribir por Gmail</a>
    <button type="button" id="daibot-copy">📋 Copiar correo: profedaianapc@gmail.com</button>
  </section>
</div>
<script>
/* Interacción del asistente: respuestas predefinidas, sin acceso a datos personales. */
(function(){
  'use strict';
  const widget=document.getElementById('daibot-widget');
  if(!widget || widget.dataset.initialized==='true') return;
  widget.dataset.initialized='true';
  const toggle=document.getElementById('daibot-toggle');
  const panel=document.getElementById('daibot-panel');
  const close=document.getElementById('daibot-close');
  const form=document.getElementById('daibot-form');
  const input=document.getElementById('daibot-input');
  const messages=document.getElementById('daibot-messages');
  const hint=document.getElementById('daibot-hint');
  const copyButton=document.getElementById('daibot-copy');
  let consultas=0, hintTimer=null, lastActivity=Date.now(), pending=[];
  const avatarMarkup='<span class="dai-face-icon" aria-hidden="true"><span class="dai-screen"><span class="dai-eye"></span><span class="dai-eye"></span></span></span>';

  function normalizar(s){return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
  /* Respuestas ordenadas desde los casos específicos hacia los generales. */
  function responder(texto){
    const q=normalizar(texto);
    if(/gracias|chau|adios|hasta luego/.test(q)) return '¡De nada! Me encantó ayudarte. Si necesitás algo más, acá estoy.';
    if(/hola|buenas|buen dia|buenas tardes|buenas noches/.test(q)) return '¡Hola! Soy DaiBot, la asistente de PetCare. Podés preguntarme cómo registrar una mascota, iniciar sesión, gestionar consultas o contactar a la responsable.';
    if(/contacto|correo|email|mail|escribir(te|les)?|comunicarme/.test(q)) return 'Podés usar «Escribir por Gmail» al pie del chat. Se abrirá Gmail en otra pestaña. También podés copiar la dirección profedaianapc@gmail.com.';
    if(/olvide|olvide mi|recuperar|no recuerdo/.test(q) && /clave|contrasena|password/.test(q)) return 'Si olvidaste tu contraseña, contactá a la responsable mediante el botón de correo. Por seguridad, no compartas contraseñas en el chat.';
    if(/cambiar|modificar|actualizar/.test(q) && /clave|contrasena|password/.test(q)) return 'Ingresá a tu cuenta y buscá la opción de cambiar contraseña. Elegí una clave segura y no la compartas.';
    if(/iniciar sesion|ingresar|entrar|login|acceder/.test(q)) return 'Desde la página principal seleccioná «Iniciar sesión», ingresá tu correo y contraseña y accedé a las funciones disponibles para tu rol.';
    if(/registrarme|registrar usuario|crear cuenta|registro|darme de alta/.test(q)) return 'En la página principal elegí «Registrarse», completá los datos solicitados y enviá el formulario. Después podrás iniciar sesión con tus credenciales.';
    if(/permiso|rol|administrador|usuario comun/.test(q)) return 'PetCare utiliza tres roles: administrador, veterinario y usuario. Cada rol tiene permisos diferentes; algunas operaciones están restringidas para proteger la información.';
    if(/error|falla|no funciona|no puedo|problema|pantalla blanca/.test(q)) return 'Comprobá que hayas iniciado sesión, revisá los datos del formulario y actualizá la página. Si el problema continúa, usá «Escribir por Gmail» e indicá qué intentabas hacer y qué mensaje apareció. No envíes tu contraseña.';
    if(/eliminar|borrar|quitar/.test(q)) return 'Para eliminar un registro, ingresá a la sección correspondiente y buscá la acción disponible. La opción puede estar restringida según tu rol. Verificá el registro antes de confirmar.';
    if(/editar|modificar|actualizar datos/.test(q)) return 'Abrí la sección correspondiente, localizá el registro y utilizá la opción de edición si tu rol tiene permiso. Guardá los cambios y comprobá que se reflejen en el listado.';
    if(/buscar|encontrar|listar|ver datos/.test(q)) return 'Entrá a la sección que necesitás desde la página principal. Allí podrás revisar los registros y utilizar las opciones disponibles según tu rol.';
    if(/vacuna|vacunacion|enfermo|sintoma|urgencia|emergencia/.test(q)) return 'Puedo ayudarte a usar PetCare, pero no reemplazo la atención veterinaria. Si tu mascota está enferma o es una urgencia, comunicate con un profesional veterinario.';
    if(/consulta|diagnostico|atencion|historial|turno|precio/.test(q)) return 'En «Consultas» se registran las atenciones veterinarias, con fecha, diagnóstico, precio, mascota y profesional. Las opciones de carga y modificación dependen de tu rol. PetCare no confirma turnos automáticamente.';
    if(/veterinario|profesional|matricula|especialidad/.test(q)) return 'En «Veterinarios» podés consultar los profesionales registrados y, si tenés permisos, administrar su matrícula, especialidad y datos personales.';
    if(/cliente|dueno|propietario|dni|telefono/.test(q)) return 'En «Clientes» se gestionan los datos de los propietarios, como nombre, apellido, DNI y teléfono. Las acciones disponibles dependen de tu rol.';
    if(/mascota|perro|gato|animal|raza|especie/.test(q)) return 'En «Mascotas» podés consultar los animales registrados y, si tenés permiso, agregar o editar datos como nombre, especie, raza, fecha de nacimiento y propietario.';
    if(/contrasena|clave|password/.test(q)) return 'Las contraseñas son privadas. Si ya ingresaste a PetCare, utilizá la función de cambio de contraseña. Si no podés acceder, escribí al correo de contacto.';
    if(/que es petcare|para que sirve|ayuda|que podes hacer/.test(q)) return 'PetCare es un sistema de gestión veterinaria. Permite organizar clientes, mascotas, profesionales y consultas, con acceso diferenciado según el rol. Puedo orientarte sobre esas funciones.';
    return 'Todavía no tengo una respuesta específica para eso. Probá preguntarme por registro, inicio de sesión, mascotas, clientes, veterinarios, consultas, permisos o contraseñas. También podés escribir a la responsable desde el botón de correo.';
  }
  function agregarMensaje(texto,esUsuario,esEscritura){
    const linea=document.createElement('div');
    linea.className='dai-line'+(esUsuario?' dai-line-user':'')+(esEscritura?' dai-typing':'');
    if(!esUsuario)linea.insertAdjacentHTML('beforeend',avatarMarkup);
    const burbuja=document.createElement('div');
    burbuja.className='dai-bubble';
    burbuja.textContent=texto; // Evita interpretar HTML ingresado por el usuario.
    linea.appendChild(burbuja);
    messages.appendChild(linea);
    messages.scrollTop=messages.scrollHeight;
    return linea;
  }
  function animar(){toggle.classList.remove('dai-excited');void toggle.offsetWidth;toggle.classList.add('dai-excited');}
  function mostrarHint(){if(panel.style.display==='flex')return;hint.style.display='block';clearTimeout(hintTimer);hintTimer=setTimeout(()=>{hint.style.display='none';},5500);}
  function abrir(){panel.style.display='flex';toggle.setAttribute('aria-expanded','true');hint.style.display='none';clearTimeout(hintTimer);animar();input.focus();}
  function cerrar(){panel.style.display='none';toggle.setAttribute('aria-expanded','false');toggle.focus();}
  agregarMensaje('¡Hola! Soy DaiBot, tu asistente de PetCare. ¿En qué puedo ayudarte?',false,false);
  toggle.addEventListener('click',()=>panel.style.display==='flex'?cerrar():abrir());
  close.addEventListener('click',cerrar);
  copyButton.addEventListener('click', async function(){
    const correo='profedaianapc@gmail.com';
    try {
      if(navigator.clipboard && window.isSecureContext){
        await navigator.clipboard.writeText(correo);
      } else {
        const campo=document.createElement('textarea');
        campo.value=correo;campo.style.position='fixed';campo.style.opacity='0';
        document.body.appendChild(campo);campo.select();
        const copiado=document.execCommand('copy');campo.remove();
        if(!copiado) throw new Error('No se pudo copiar');
      }
      copyButton.textContent='¡Correo copiado! Pegalo en Gmail';
    } catch(err){
      copyButton.textContent='Correo: '+correo;
    }
  });
  form.addEventListener('submit',function(e){
    e.preventDefault();
    const pregunta=input.value.trim();if(!pregunta)return;
    consultas++;lastActivity=Date.now();
    agregarMensaje(pregunta,true,false);input.value='';animar();
    const escribiendo=agregarMensaje('DaiBot está escribiendo ',false,true);
    const puntos=document.createElement('span');puntos.className='dai-typing-dots';puntos.textContent='...';
    escribiendo.querySelector('.dai-bubble').appendChild(puntos);
    const timer=setTimeout(function(){escribiendo.remove();agregarMensaje(responder(pregunta),false,false);animar();const i=pending.indexOf(timer);if(i!==-1)pending.splice(i,1);},2100);
    pending.push(timer);input.focus();
  });
  setTimeout(()=>{if(consultas===0)mostrarHint();},10000);
  setInterval(()=>{if(panel.style.display!=='flex' && Date.now()-lastActivity>120000)mostrarHint();},120000);
})();
</script>
