
  /* ---------- a real sample package: files built in the page, saved through the viewer ---------- */
  function crc32(u8) { var c, crc = -1; for (var i = 0; i < u8.length; i++) { c = (crc ^ u8[i]) & 0xff; for (var k = 0; k < 8; k++) c = c & 1 ? (c >>> 1) ^ 0xEDB88320 : c >>> 1; crc = (crc >>> 8) ^ c; } return (crc ^ -1) >>> 0; }
  function zipParts(files) {
    var enc = new TextEncoder(), parts = [], central = [], off = 0;
    files.forEach(function (f) {
      var name = enc.encode(f.name), data = enc.encode(f.text), crc = crc32(data);
      var lh = new Uint8Array(30 + name.length), v = new DataView(lh.buffer);
      v.setUint32(0, 0x04034b50, true); v.setUint16(4, 20, true); v.setUint16(6, 0x0800, true); v.setUint16(12, 0x21, true); v.setUint32(14, crc, true); v.setUint32(18, data.length, true); v.setUint32(22, data.length, true); v.setUint16(26, name.length, true); lh.set(name, 30);
      var ch = new Uint8Array(46 + name.length), w = new DataView(ch.buffer);
      w.setUint32(0, 0x02014b50, true); w.setUint16(4, 20, true); w.setUint16(6, 20, true); w.setUint16(8, 0x0800, true); w.setUint16(14, 0x21, true); w.setUint32(16, crc, true); w.setUint32(20, data.length, true); w.setUint32(24, data.length, true); w.setUint16(28, name.length, true); w.setUint32(42, off, true); ch.set(name, 46);
      parts.push(lh, data); central.push(ch); off += lh.length + data.length;
    });
    var csize = central.reduce(function (n, c) { return n + c.length; }, 0), end = new Uint8Array(22), e = new DataView(end.buffer);
    e.setUint32(0, 0x06054b50, true); e.setUint16(8, files.length, true); e.setUint16(10, files.length, true); e.setUint32(12, csize, true); e.setUint32(16, off, true);
    return parts.concat(central, [end]);
  }
