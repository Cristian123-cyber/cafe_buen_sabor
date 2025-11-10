import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

/**
 * Configuración por defecto de la empresa
 */
const defaultCompanyInfo = {
  name: 'Mi Empresa',
  address: 'Dirección de la empresa',
  phone: 'Teléfono: (123) 456-7890',
  email: 'info@miempresa.com',
  taxId: 'NIT: 123456789-0'
};

/**
 * Formatea una fecha al formato DD/MM/YYYY HH:mm
 * @param {string} dateString - Fecha en formato string
 * @returns {string} Fecha formateada
 */
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${day}/${month}/${year} ${hours}:${minutes}`;
};

/**
 * Formatea un número como moneda colombiana
 * @param {number} amount - Cantidad a formatear
 * @returns {string} Cantidad formateada
 */
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

/**
 * Dibuja el header de la factura
 * @param {jsPDF} doc - Documento PDF
 * @param {Object} companyInfo - Información de la empresa
 */
const drawHeader = (doc, companyInfo) => {
  const pageWidth = doc.internal.pageSize.getWidth();
  const primaryColor = [41, 128, 185];

  // Fondo del header
  doc.setFillColor(...primaryColor);
  doc.rect(0, 0, pageWidth, 40, 'F');
  
  // Información de la empresa
  doc.setTextColor(255, 255, 255);
  doc.setFontSize(22);
  doc.setFont('helvetica', 'bold');
  doc.text(companyInfo.name, 15, 20);
  
  doc.setFontSize(9);
  doc.setFont('helvetica', 'normal');
  doc.text(companyInfo.address, 15, 27);
  doc.text(`${companyInfo.phone} | ${companyInfo.email}`, 15, 32);
  doc.text(companyInfo.taxId, 15, 37);

  // Título FACTURA
  doc.setFontSize(24);
  doc.setFont('helvetica', 'bold');
  const titleWidth = doc.getTextWidth('FACTURA');
  doc.text('FACTURA', pageWidth - titleWidth - 15, 25);
};

/**
 * Dibuja la información de la factura
 * @param {jsPDF} doc - Documento PDF
 * @param {Object} invoiceData - Datos de la factura
 * @returns {number} Posición Y final
 */
const drawInvoiceInfo = (doc, invoiceData) => {
  const darkGray = [52, 73, 94];
  const primaryColor = [41, 128, 185];
  let yPosition = 50;

  doc.setTextColor(...darkGray);
  doc.setFontSize(10);

  // Número de factura
  doc.setFont('helvetica', 'bold');
  doc.text('Factura No:', 15, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(`${invoiceData.id_sale}`, 50, yPosition);

  // Fecha
  yPosition += 6;
  doc.setFont('helvetica', 'bold');
  doc.text('Fecha:', 15, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(formatDate(invoiceData.created_at), 50, yPosition);

  // Cajero
  yPosition += 6;
  doc.setFont('helvetica', 'bold');
  doc.text('Cajero:', 15, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(invoiceData.cashier_name, 50, yPosition);

  // Email
  yPosition += 6;
  doc.setFont('helvetica', 'bold');
  doc.text('Email:', 15, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(invoiceData.cashier_email, 50, yPosition);

  // Línea separadora
  yPosition += 12;
  doc.setDrawColor(...primaryColor);
  doc.setLineWidth(0.5);
  doc.line(15, yPosition, doc.internal.pageSize.getWidth() - 15, yPosition);

  return yPosition + 10;
};

/**
 * Prepara los datos de productos para la tabla
 * @param {Object} invoiceData - Datos de la factura
 * @returns {Object} Datos de tabla y subtotal
 */
const prepareTableData = (invoiceData) => {
  const tableData = [];
  let subtotal = 0;

  invoiceData.orders.forEach(order => {
    order.products.forEach(product => {
      const quantity = product.quantity;
      const unitPrice = parseFloat(product.product_price);
      const total = quantity * unitPrice;
      subtotal += total;

      tableData.push([
        product.product_name,
        quantity.toString(),
        formatCurrency(unitPrice),
        formatCurrency(total)
      ]);
    });
  });

  return { tableData, subtotal };
};

/**
 * Dibuja la tabla de productos
 * @param {jsPDF} doc - Documento PDF
 * @param {Array} tableData - Datos de la tabla
 * @param {number} startY - Posición Y inicial
 * @returns {number} Posición Y final
 */
const drawProductsTable = (doc, tableData, startY) => {
  const primaryColor = [41, 128, 185];
  const darkGray = [52, 73, 94];

  autoTable(doc, {
    startY,
    head: [['Producto', 'Cantidad', 'Precio Unit.', 'Total']],
    body: tableData,
    theme: 'striped',
    headStyles: {
      fillColor: primaryColor,
      textColor: [255, 255, 255],
      fontSize: 10,
      fontStyle: 'bold',
      halign: 'center'
    },
    bodyStyles: {
      fontSize: 9,
      textColor: darkGray
    },
    columnStyles: {
      0: { halign: 'left', cellWidth: 80 },
      1: { halign: 'center', cellWidth: 30 },
      2: { halign: 'right', cellWidth: 35 },
      3: { halign: 'right', cellWidth: 35 }
    },
    margin: { left: 15, right: 15 }
  });

  return doc.lastAutoTable.finalY + 10;
};

/**
 * Dibuja la sección de totales
 * @param {jsPDF} doc - Documento PDF
 * @param {number} subtotal - Subtotal de la factura
 * @param {number} yPosition - Posición Y inicial
 */
const drawTotals = (doc, subtotal, yPosition) => {
  const pageWidth = doc.internal.pageSize.getWidth();
  const primaryColor = [41, 128, 185];
  const totalsX = pageWidth - 70;

  // Fondo de totales
  doc.setFillColor(245, 245, 245);
  doc.rect(totalsX - 5, yPosition - 5, 60, 25, 'F');

  doc.setFontSize(10);
  
  // Subtotal
  doc.setFont('helvetica', 'bold');
  doc.text('Subtotal:', totalsX, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(formatCurrency(subtotal), pageWidth - 20, yPosition, { align: 'right' });

  // IVA
  yPosition += 6;
  const tax = subtotal * 0; // Ajustar si hay impuestos
  doc.setFont('helvetica', 'bold');
  doc.text('IVA (0%):', totalsX, yPosition);
  doc.setFont('helvetica', 'normal');
  doc.text(formatCurrency(tax), pageWidth - 20, yPosition, { align: 'right' });

  // Línea separadora
  yPosition += 8;
  doc.setDrawColor(...primaryColor);
  doc.setLineWidth(0.5);
  doc.line(totalsX - 5, yPosition - 2, pageWidth - 15, yPosition - 2);

  // Total
  yPosition += 4;
  doc.setFontSize(12);
  doc.setFont('helvetica', 'bold');
  doc.setTextColor(...primaryColor);
  doc.text('TOTAL:', totalsX, yPosition);
  doc.text(formatCurrency(subtotal + tax), pageWidth - 20, yPosition, { align: 'right' });
};

/**
 * Dibuja el footer de la factura
 * @param {jsPDF} doc - Documento PDF
 */
const drawFooter = (doc) => {
  const pageWidth = doc.internal.pageSize.getWidth();
  const footerY = doc.internal.pageSize.getHeight() - 20;
  
  doc.setFontSize(8);
  doc.setTextColor(128, 128, 128);
  doc.setFont('helvetica', 'italic');
  doc.text('Gracias por su compra', pageWidth / 2, footerY, { align: 'center' });
  doc.text('Esta es una factura válida', pageWidth / 2, footerY + 4, { align: 'center' });
};

/**
 * Genera una factura en PDF
 * @param {Object} invoiceData - Datos de la factura desde la API
 * @param {Object} options - Opciones de configuración
 * @param {Object} options.companyInfo - Información de la empresa
 * @param {string} options.action - 'download' | 'open' | 'blob' | 'base64'
 * @param {string} options.filename - Nombre del archivo (solo para download)
 * @returns {Promise<Blob|string|void>}
 */
export const generateInvoice = async (invoiceData, options = {}) => {
  const {
    companyInfo = defaultCompanyInfo,
    action = 'download',
    filename = `factura-${invoiceData.id_sale}.pdf`
  } = options;

  try {
    // Crear documento PDF
    const doc = new jsPDF();

    // Dibujar secciones
    drawHeader(doc, companyInfo);
    const infoEndY = drawInvoiceInfo(doc, invoiceData);
    
    const { tableData, subtotal } = prepareTableData(invoiceData);
    const tableEndY = drawProductsTable(doc, tableData, infoEndY);
    
    drawTotals(doc, subtotal, tableEndY);
    drawFooter(doc);

    // Ejecutar acción según lo solicitado
    switch (action) {
      case 'download':
        doc.save(filename);
        break;
      
      case 'open':
        window.open(doc.output('bloburl'), '_blank');
        break;
      
      case 'blob':
        return doc.output('blob');
      
      case 'base64':
        return doc.output('datauristring');
      
      default:
        doc.save(filename);
    }
  } catch (error) {
    console.error('Error al generar la factura:', error);
    throw new Error('No se pudo generar la factura PDF');
  }
};

/**
 * Composable para Vue 3
 * @param {Object} customCompanyInfo - Información personalizada de la empresa
 * @returns {Object} Funciones para generar facturas
 */
export const useInvoiceGenerator = (customCompanyInfo = {}) => {
  const companyInfo = { ...defaultCompanyInfo, ...customCompanyInfo };

  const generate = (invoiceData, action = 'download', filename) => {
    return generateInvoice(invoiceData, { companyInfo, action, filename });
  };

  const download = (invoiceData, filename) => {
    return generate(invoiceData, 'download', filename);
  };

  const open = (invoiceData) => {
    return generate(invoiceData, 'open');
  };

  const getBlob = (invoiceData) => {
    return generate(invoiceData, 'blob');
  };

  const getBase64 = (invoiceData) => {
    return generate(invoiceData, 'base64');
  };

  return {
    generate,
    download,
    open,
    getBlob,
    getBase64
  };
};

export default generateInvoice;