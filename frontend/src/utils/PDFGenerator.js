// src/utils/pdfGenerator.js
import { jsPDF } from 'jspdf';
import { autoTable } from 'jspdf-autotable';

/**
 * Añade una cabecera y un pie de página estandarizados a cualquier documento PDF.
 * @param {jsPDF} doc - La instancia del documento jsPDF.
 * @param {string} reportTitle - El título del reporte para la cabecera.
 */
const addHeaderFooter = (doc, reportTitle) => {
  const pageCount = doc.internal.getNumberOfPages();
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();
  const today = new Date().toLocaleDateString('es-ES', {
    year: 'numeric', month: 'long', day: 'numeric'
  });

  for (let i = 1; i <= pageCount; i++) {
    doc.setPage(i);
    // --- CABECERA ---
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('Café Buen Sabor', 14, 20);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.text(reportTitle, pageWidth - 14, 15, { align: 'right' });
    doc.text(`Generado: ${today}`, pageWidth - 14, 20, { align: 'right' });
    // Línea separadora
    doc.setDrawColor(200);
    doc.line(14, 25, pageWidth - 14, 25);
    // --- PIE DE PÁGINA ---
    doc.setFontSize(8);
    doc.text(
      `Página ${i} de ${pageCount}`,
      pageWidth / 2,
      pageHeight - 10,
      { align: 'center' }
    );
  }
};

/**
 * Formatea un número como moneda local (ej. Colombiana).
 * @param {number} value El número a formatear.
 * @returns {string} El número formateado como moneda.
 */
const formatCurrency = (value) => {
  if (typeof value !== 'number') return '$0';
  return `$${value.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
};

export const pdfGenerator = {
  /**
   * 1. REPORTE: BALANCE GENERAL DE VENTAS
   */
  createSalesBalancePDF: (data, startDate, endDate) => {
    const doc = new jsPDF();
    const { summary, sales } = data;

    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Balance General de Ventas', 14, 40);
    doc.setFontSize(12);
    doc.setFont('helvetica', 'normal');
    doc.text(`Periodo: ${startDate} al ${endDate}`, 14, 47);

    // Sección de Resumen - usando autoTable como función separada
    autoTable(doc, {
      body: [
        ['Ingresos Totales', formatCurrency(summary.total_revenue)],
        ['Ventas Realizadas', summary.total_sales_count.toString()],
        ['Valor Promedio Venta', formatCurrency(summary.average_sale_value)],
        ['Ingresos Contado', formatCurrency(summary.payment_methods.CONTADO || 0)],
        ['Ingresos Transferencia', formatCurrency(summary.payment_methods.TRANSFERENCIA || 0)],
      ],
      startY: 55,
      theme: 'plain',
      styles: { fontSize: 11 },
      columnStyles: { 0: { fontStyle: 'bold' } }
    });

    // Tabla de Ventas Detalladas
    const tableColumn = ["ID Venta", "Fecha", "Cajero", "Método Pago", "Total"];
    const tableRows = sales.map(sale => [
      sale.sale_id,
      new Date(sale.sale_date).toLocaleString('es-ES'),
      sale.cashier_name,
      sale.payment_method,
      formatCurrency(sale.total_amount)
    ]);

    autoTable(doc, {
      head: [tableColumn],
      body: tableRows,
      startY: doc.lastAutoTable.finalY + 10,
      theme: 'grid',
      headStyles: { fillColor: [38, 102, 226] }
    });

    addHeaderFooter(doc, 'Reporte de Ventas');
    doc.save(`BalanceVentas_${startDate}_${endDate}.pdf`);
  },

  /**
   * 2. REPORTE: LISTADO DE FACTURAS
   */
  createInvoicesListPDF: (data, startDate, endDate) => {
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Listado de Facturas Emitidas', 14, 40);
    doc.setFontSize(12);
    doc.setFont('helvetica', 'normal');
    doc.text(`Periodo: ${startDate} al ${endDate}`, 14, 47);
    
    const tableColumn = ["ID Factura", "Fecha", "Cajero", "Método", "Total", "Pedidos Incluidos"];
    const tableRows = data.map(invoice => [
        invoice.invoice_id,
        new Date(invoice.invoice_date).toLocaleString('es-ES'),
        invoice.cashier_name,
        invoice.payment_method,
        formatCurrency(invoice.total_amount),
        invoice.included_orders.map(o => `#${o.order_id} (Mesa ${o.table_number})`).join(', ')
    ]);

    autoTable(doc, {
        head: [tableColumn],
        body: tableRows,
        startY: 55,
        theme: 'striped',
        headStyles: { fillColor: [22, 163, 74] } // Verde
    });

    addHeaderFooter(doc, 'Listado de Facturas');
    doc.save(`ListadoFacturas_${startDate}_${endDate}.pdf`);
  },

  /**
   * 3. REPORTE: DESEMPEÑO DE EMPLEADOS
   */
  createEmployeesPerformancePDF: (data, startDate, endDate) => {
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Reporte de Desempeño de Empleados', 14, 40);
    doc.setFontSize(12);
    doc.setFont('helvetica', 'normal');
    doc.text(`Periodo: ${startDate} al ${endDate}`, 14, 47);

    // Separamos los datos por rol para generar tablas distintas
    const waiters = data.filter(e => e.role === 'Mesero');
    const cashiers = data.filter(e => e.role === 'Cajero');
    
    let lastY = 55;

    if (waiters.length > 0) {
        doc.setFontSize(14);
        doc.text('Desempeño de Meseros', 14, lastY);
        const waiterColumns = ["Empleado", "Órdenes Confirmadas", "Valor Gestionado", "Promedio por Orden"];
        const waiterRows = waiters.map(e => [
            e.employee_name,
            e.metrics.orders_confirmed,
            formatCurrency(e.metrics.total_value_managed),
            formatCurrency(e.metrics.average_order_value)
        ]);
        
        autoTable(doc, { 
          head: [waiterColumns], 
          body: waiterRows, 
          startY: lastY + 5, 
          theme: 'grid', 
          headStyles: { fillColor: [217, 119, 6] } // Naranja
        });
        lastY = doc.lastAutoTable.finalY;
    }

    if (cashiers.length > 0) {
        doc.setFontSize(14);
        doc.text('Desempeño de Cajeros', 14, lastY + 15);
        const cashierColumns = ["Empleado", "Ventas Procesadas", "Ingresos Procesados", "Promedio por Venta"];
        const cashierRows = cashiers.map(e => [
            e.employee_name,
            e.metrics.sales_processed,
            formatCurrency(e.metrics.total_revenue_processed),
            formatCurrency(e.metrics.average_sale_value)
        ]);
        
        autoTable(doc, { 
          head: [cashierColumns], 
          body: cashierRows, 
          startY: lastY + 20, 
          theme: 'grid', 
          headStyles: { fillColor: [59, 130, 246] } // Azul
        });
    }

    addHeaderFooter(doc, 'Desempeño de Empleados');
    doc.save(`DesempenoEmpleados_${startDate}_${endDate}.pdf`);
  },

  /**
   * 4. REPORTE: ESTADO DEL INVENTARIO
   */
  createInventoryStatusPDF: (data) => {
    const doc = new jsPDF();
    console.log(data);
    const { products } = data;

    console.log(products);
    
    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Reporte de Estado de Inventario', 14, 40);
    
    let lastY = 45;

    // Tabla de Productos con stock
    if (products.length > 0) {
        doc.setFontSize(14);
        doc.text('Productos Finales', 14, lastY + 10);
        const productColumns = ["ID", "Producto", "Categoría", "Stock Actual", "Nivel Bajo", "Estado"];
        const productRows = products.map(p => [
            p.product_id,
            p.product_name,
            p.category,
            `${p.stock} ${p.stock_unit}`,
            p.low_stock_level,
            p.status
        ]);
        
        autoTable(doc, { 
          head: [productColumns], 
          body: productRows, 
          startY: lastY + 15, 
          theme: 'grid', 
          headStyles: { fillColor: [132, 40, 226] }, // Morado
          didParseCell: function(data) { // Colorear celdas de estado
              if (data.column.index === 5) { // Columna de "Estado"
                  if (data.cell.raw === 'BAJO') {
                      data.cell.styles.fillColor = [251, 191, 36]; // Amarillo en RGB
                      data.cell.styles.textColor = [0, 0, 0];
                  } else if (data.cell.raw === 'CRITICO' || data.cell.raw === 'AGOTADO') {
                      data.cell.styles.fillColor = [239, 68, 68]; // Rojo en RGB
                      data.cell.styles.textColor = [255, 255, 255];
                  }
              }
          }
        });
        lastY = doc.lastAutoTable.finalY;
    }
    
    

    addHeaderFooter(doc, 'Estado de Inventario');
    doc.save(`EstadoInventario_${new Date().toISOString().split('T')[0]}.pdf`);
  }
};