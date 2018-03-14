package org.swissphpfriends.gradle.task

import org.gradle.api.Task
import org.junit.Test
import org.gradle.testfixtures.ProjectBuilder
import org.gradle.api.Project

import static org.junit.Assert.*

class ComposerInstallTest {

    @Test
    public void canAddTaskToProject() {
        Project project = ProjectBuilder.builder().build()
        def task = project.task('composerInstall', type: ComposerInstall)
        assertTrue(task instanceof ComposerInstall)
    }

    @Test
    public void installsComposerPharAndDependencies() {
        Task composerInstallTask = this.getTaskInstance();

        assertTrue(composerInstallTask.hasProperty('workingDirectory'))
        assertEquals('./',composerInstallTask.getProperty('workingDirectory'))
        composerInstallTask.setProperty('workingDirectory', '../user', );
        assertEquals('../user',composerInstallTask.getProperty('workingDirectory'))

        composerInstallTask.run();

        File composerBinary = new File('../user/composer.phar');
        assertTrue(composerBinary.exists());
        assertTrue(composerBinary.canExecute());

        File vendorDir = new File('../user/vendor');
        assertTrue(vendorDir.exists());
        assertTrue(vendorDir.isDirectory());
        assertTrue(vendorDir.canRead());

        File composerInstaller = new File('../user/installer');
        assertFalse(composerInstaller.exists());
    }

    private Task getTaskInstance() {
        Project project = ProjectBuilder.builder().build()
        return project.task('phpunit', type: ComposerInstall)
    }
}
