/**
 * Funções utilitárias
 */
class Utils {
    /**
     * Detecta se o SDP é para uma chamada de vídeo
     */
    static isVideoCallSDP(sdpString) {
        if (!sdpString || typeof sdpString !== 'string') {
            return false;
        }
        
        const lines = sdpString.split('\n');
        for (const line of lines) {
            const trimmed = line.trim();
            if (trimmed.startsWith('m=video ')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Limpa e valida SDP
     */
    static cleanSDP(sdpString) {
        if (!sdpString || typeof sdpString !== 'string') {
            return null;
        }
        
        let cleaned = sdpString.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        
        const lineCount = cleaned.split('\n').filter(l => l.trim()).length;
        const hasNewlines = cleaned.includes('\n');
        const isSingleLine = !hasNewlines || (lineCount === 1 && cleaned.length > 100);
        const needsReconstruction = isSingleLine || (lineCount < 3 && cleaned.length > 100);
        
        if (needsReconstruction) {
            console.warn('SDP appears to be on single line or malformed, attempting to reconstruct...');
            
            cleaned = cleaned.replace(/([a-z])=([^\n]*?)([a-z])=/gi, (match, p1, p2, p3) => {
                if (p1.toLowerCase() === 'c' && p2.length < 5 && /^\d+$/.test(p2)) {
                    return match;
                }
                if (p1.toLowerCase() === 'c' && /\d+\.\d+\.\d+\.\d+/.test(p2)) {
                    return match;
                }
                if (p2.length > 0 && p2.length < 1000) {
                    return `${p1}=${p2}\n${p3}=`;
                }
                return match;
            });
            
            cleaned = cleaned.replace(/^\n+/, '');
            cleaned = cleaned.replace(/\n\n+/g, '\n');
        }
        
        cleaned = cleaned.replace(/[\x00-\x08\x0B-\x1F\x7F]/g, '');
        cleaned = cleaned.replace(/[\u0000-\u0008\u000B-\u001F\u007F-\u009F]/g, '');
        
        const lines = cleaned.split('\n');
        const processedLines = [];
        
        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            
            if (!line) continue;
            
            if (line.length > 0 && !/^[a-z]=/i.test(line) && processedLines.length > 0) {
                const lastLine = processedLines[processedLines.length - 1];
                if (lastLine && lastLine.startsWith('c=')) {
                    const cValue = lastLine.substring(2).trim();
                    if (/^\d+$/.test(cValue) || (cValue.length < 10 && !cValue.includes('IN'))) {
                        processedLines[processedLines.length - 1] = lastLine + ' ' + line;
                        continue;
                    }
                }
                if (lastLine && !lastLine.endsWith(' ') && !/^[a-z]=/i.test(line)) {
                    processedLines[processedLines.length - 1] = lastLine + ' ' + line;
                    continue;
                }
            }
            
            if (line.startsWith('c=')) {
                line = line.replace(/[^\x20-\x7E]/g, '');
                line = line.trim();
                
                const cMatch = line.match(/^c=(.+)$/);
                if (cMatch) {
                    const cValue = cMatch[1].trim();
                    if (/^\d+$/.test(cValue) || cValue.length < 5) {
                        console.warn('Malformed c= line detected, fixing:', line);
                        const ipMatch = line.match(/(\d+\.\d+\.\d+\.\d+)/);
                        if (ipMatch) {
                            line = `c=IN IP4 ${ipMatch[1]}`;
                        } else {
                            line = 'c=IN IP4 0.0.0.0';
                        }
                    } else if (!cValue.includes('IN') && !cValue.includes('IP')) {
                        const ipMatch = cValue.match(/(\d+\.\d+\.\d+\.\d+)/);
                        if (ipMatch) {
                            line = `c=IN IP4 ${ipMatch[1]}`;
                        } else {
                            line = 'c=IN IP4 0.0.0.0';
                        }
                    }
                }
            }
            
            if (line.startsWith('a=')) {
                if (line.includes('ssrc:') && line.includes('msid:')) {
                    line = line.replace(/[^\x20-\x7E]/g, '');
                    line = line.replace(/[\t]+/g, ' ');
                    line = line.replace(/\s+/g, ' ').trim();
                    
                    let ssrcMatch = line.match(/a=ssrc:(\d+)\s+msid:([a-f0-9\-]+)\s+([a-f0-9\-]+)/i);
                    
                    if (!ssrcMatch) {
                        ssrcMatch = line.match(/ssrc:(\d+)\s+msid:([a-f0-9\-]+)\s+([a-f0-9\-]+)/i);
                        if (ssrcMatch) {
                            line = `a=ssrc:${ssrcMatch[1]} msid:${ssrcMatch[2]} ${ssrcMatch[3]}`;
                        }
                    } else {
                        line = `a=ssrc:${ssrcMatch[1]} msid:${ssrcMatch[2]} ${ssrcMatch[3]}`;
                    }
                    
                    if (!ssrcMatch) {
                        const parts = line.split(/\s+/);
                        const ssrcPart = parts.find(p => p.startsWith('ssrc:'));
                        const msidPart = parts.find(p => p.startsWith('msid:'));
                        const trackPart = parts.find((p, i) => i > parts.indexOf(msidPart) && !p.includes(':'));
                        
                        if (ssrcPart && msidPart && trackPart) {
                            const ssrcId = ssrcPart.replace('ssrc:', '');
                            const msid = msidPart.replace('msid:', '');
                            line = `a=ssrc:${ssrcId} msid:${msid} ${trackPart}`;
                        } else {
                            const ssrcOnly = line.match(/ssrc:(\d+)/);
                            if (ssrcOnly) {
                                line = `a=ssrc:${ssrcOnly[1]}`;
                                console.warn('Simplified ssrc line (removed msid):', line);
                            } else {
                                console.warn('Removing unparseable ssrc line:', line);
                                continue;
                            }
                        }
                    }
                } else {
                    line = line.replace(/[^\x20-\x7E\n]/g, '');
                    line = line.replace(/\s+/g, ' ').trim();
                }
            } else {
                line = line.replace(/[^\x20-\x7E\n]/g, '');
                line = line.trim();
            }
            
            if (line.length > 0) {
                processedLines.push(line);
            }
        }
        
        cleaned = processedLines.join('\n');
        
        if (!cleaned.endsWith('\n')) {
            cleaned += '\n';
        }
        
        const finalLines = cleaned.split('\n').filter(l => l.trim());
        if (finalLines.length < 3) {
            console.error('SDP has too few lines after processing:', finalLines.length);
            return null;
        }
        
        if (!finalLines[0].trim().startsWith('v=')) {
            console.error('SDP does not start with version line. First line:', finalLines[0]);
            const vLineIndex = finalLines.findIndex(l => l.trim().startsWith('v='));
            if (vLineIndex > 0) {
                const vLine = finalLines.splice(vLineIndex, 1)[0];
                finalLines.unshift(vLine);
                cleaned = finalLines.join('\n') + '\n';
                console.warn('Moved v= line to beginning');
            } else {
                console.error('No v= line found in SDP');
                return null;
            }
        }
        
        return cleaned;
    }
}

