/**
 * تطبيق نظام التصويت الإلكتروني
 */

class VotingApp {
    constructor() {
        this.candidates = [];
        this.selectedCandidate = null;
        this.deviceFingerprint = null;
        this.hasVoted = false;
        this.init();
    }

    /**
     * تهيئة التطبيق
     */
    async init() {
        this.generateDeviceFingerprint();
        await this.loadCandidates();
        await this.checkVoteStatus();
        this.attachEventListeners();
    }

    /**
     * إنشاء بصمة الجهاز
     */
    generateDeviceFingerprint() {
        const fingerprint = {
            userAgent: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            screenResolution: `${window.screen.width}x${window.screen.height}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            timestamp: new Date().toDateString(),
        };

        const str = JSON.stringify(fingerprint);
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }

        this.deviceFingerprint = `device-${Math.abs(hash).toString(36)}`;
        localStorage.setItem('deviceFingerprint', this.deviceFingerprint);
    }

    /**
     * تحميل المرشحين
     */
    async loadCandidates() {
        try {
            const response = await fetch('api/get_candidates.php');
            const data = await response.json();

            if (data.success) {
                this.candidates = data.candidates;
                this.renderCandidates();
            } else {
                this.showAlert('خطأ في تحميل البيانات', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('خطأ في الاتصال بالخادم', 'error');
        }
    }

    /**
     * عرض المرشحين
     */
    renderCandidates() {
        const grid = document.getElementById('candidatesGrid');
        grid.innerHTML = '';

        this.candidates.forEach((candidate, index) => {
            const headerColor = index % 2 === 0 ? 'red' : 'black';
            const card = document.createElement('div');
            card.className = 'candidate-card';
            card.innerHTML = `
                <div class="candidate-header ${headerColor}">
                    ${candidate.title} ${candidate.name}
                </div>
                <div class="candidate-body">
                    <div class="vote-count">${candidate.voteCount}</div>
                    <div class="vote-text">
                        ${candidate.voteCount === 1 ? 'صوت واحد' : `${candidate.voteCount} أصوات`}
                    </div>
                    ${!this.hasVoted ? `
                        <button class="candidate-btn select" onclick="app.selectCandidate(${candidate.id})">
                            اختر
                        </button>
                    ` : ''}
                </div>
            `;

            if (this.selectedCandidate === candidate.id) {
                card.classList.add('selected');
            }

            grid.appendChild(card);
        });

        this.updateResultsChart();
    }

    /**
     * اختيار مرشح
     */
    selectCandidate(candidateId) {
        if (this.hasVoted) return;

        this.selectedCandidate = this.selectedCandidate === candidateId ? null : candidateId;
        this.renderCandidates();
    }

    /**
     * التحقق من حالة التصويت
     */
    async checkVoteStatus() {
        try {
            const response = await fetch('api/check_vote_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    deviceFingerprint: this.deviceFingerprint
                })
            });

            const data = await response.json();

            if (data.success) {
                this.hasVoted = data.hasVoted;

                if (this.hasVoted) {
                    this.showAlert('شكراً! لقد قمت بالتصويت مسبقاً. يمكنك مشاهدة نتائج التصويت أدناه.', 'info');
                    document.getElementById('voteSection').style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    /**
     * التصويت
     */
    async vote() {
        if (!this.selectedCandidate) {
            this.showAlert('يرجى اختيار مرشح', 'error');
            return;
        }

        const btn = document.querySelector('.vote-btn');
        btn.disabled = true;
        btn.classList.add('loading');
        btn.textContent = 'جاري التصويت...';

        try {
            const response = await fetch('api/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    candidateId: this.selectedCandidate,
                    deviceFingerprint: this.deviceFingerprint
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('شكراً لتصويتك! تم تسجيل صوتك بنجاح.', 'success');
                this.hasVoted = true;
                document.getElementById('voteSection').style.display = 'none';
                await this.loadCandidates();
            } else {
                this.showAlert(data.message || 'خطأ في التصويت', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('خطأ في الاتصال بالخادم', 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('loading');
            btn.textContent = 'تأكيد التصويت';
        }
    }

    /**
     * عرض رسالة تنبيه
     */
    showAlert(message, type) {
        const alertDiv = document.getElementById('alert');
        alertDiv.textContent = message;
        alertDiv.className = `alert alert-${type} show`;

        setTimeout(() => {
            alertDiv.classList.remove('show');
        }, 3000);
    }

    /**
     * تحديث رسم بياني النتائج
     */
    updateResultsChart() {
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = '';

        const maxVotes = Math.max(...this.candidates.map(c => c.voteCount), 1);

        this.candidates.forEach(candidate => {
            const percentage = (candidate.voteCount / maxVotes) * 100;
            const resultItem = document.createElement('div');
            resultItem.className = 'result-item';
            resultItem.innerHTML = `
                <div class="result-name">${candidate.title} ${candidate.name}</div>
                <div class="result-bar-container">
                    <div class="result-bar" style="width: ${percentage}%"></div>
                </div>
                <div class="result-count">${candidate.voteCount}</div>
            `;
            resultsDiv.appendChild(resultItem);
        });
    }

    /**
     * ربط أحداث الزر
     */
    attachEventListeners() {
        const voteBtn = document.querySelector('.vote-btn');
        if (voteBtn) {
            voteBtn.addEventListener('click', () => this.vote());
        }
    }
}

// تهيئة التطبيق عند تحميل الصفحة
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new VotingApp();
});
